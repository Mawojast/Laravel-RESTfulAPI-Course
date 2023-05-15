<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\ApiController;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionCategoryController extends ApiController
{
    public function __construct(){

        $this->middleware('client_credentials')->only(['index']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Transaction $transaction)
    {
        $catgeories = $transaction->product->categories;

        return $this->showAll($catgeories);
    }
}
