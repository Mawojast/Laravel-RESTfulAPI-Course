<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Models\Seller;
use Illuminate\Http\Request;

class SellerTransactionController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Seller $seller)
    {
        $transactions = $seller->products();
        $transactions = $transactions->whereHas('transactions');
        $transactions = $transactions->with('transactions');
        $transactions = $transactions->get();
        $transactions = $transactions->pluck('transactions');
        $transactions = $transactions->collapse();

        return $this->showAll($transactions);
    }
}
