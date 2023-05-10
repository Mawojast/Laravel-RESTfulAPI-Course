<?php

namespace App\Models;

use App\Transformers\BuyerTransformer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\BuyerScope;

class Buyer extends User
{
    use HasFactory;

    public $transformer = BuyerTransformer::class;
    protected $guarded = [];

    protected static function boot() {

        parent::boot();
        static::addGlobalScope(new BuyerScope);
    }
    public function transaction(){

        return $this->hasMany(Transaction::class);
    }
}
