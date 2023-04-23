<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Models\Seller;
use App\Models\User;
use App\Models\Product;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SellerProductController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Seller $seller)
    {
        $products = $seller->products;

        return $this->showAll($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, User $seller)
    {
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'quantity' => 'required|integer|min:1',
            'image' => 'required|image',
        ];

        $this->validate($request, $rules);

        $product = Product::create([
            'status' => Product::UNAVAILABLE,
            'image' => '1.jpg',
            'seller_id' => $seller->id,
            'name' => $request->name,
            'description' => $request->description,
            'quantity' => $request->quantity,
        ]);

        return $this->showOne($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Seller $seller, Product $product)
    {
        $rules = [
            'quantity' => 'required|integer|min:1',
            'image' => 'image',
            'status' => 'in: '.Product::AVAILABLE.','.Product::UNAVAILABLE,
        ];

        $this->validate($request, $rules);

        $this->checkSeller($seller, $product);

        $product->fill($request->only([
            'name',
            'description',
            'quantity',
        ]));

        if($request->has('status')){
            $product->status = $request->status;

            if($product->isAvailable() && $product->categories()->count() == 0){
                return $this->errorResponse('Active Product must have at least one category', 400);
            }
        }

        if($product->isClean()){
            return $this->errorResponse('You need to specify a different value to update', 422);
        }

        $product->save();

        return $this->showOne($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Seller $seller, Product $product)
    {
        $this->checkSeller($seller, $product);

        $product->delete();

        $this->showOne($product);
    }

    protected function checkSeller(Seller $seller, Product $product){

        if($seller->id != $product->seller_id){
            throw new HttpException(422, 'User is not authorized to update product');
        }
    }
}
