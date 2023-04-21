<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Models\Buyer;

class BuyerController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $buyers = Buyer::has('transaction')->get();

        return $this->showAll($buyers);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $buyer = Buyer::has('transaction')->findOrFAil($id);

        return $this->showOne($buyer);
    }
}
