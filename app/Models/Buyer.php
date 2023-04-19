<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buyer extends User
{
    use HasFactory;
    protected $guarded = [];

    public function transaction(){

        return $this->hasMany(Transaction::class);
    }
}