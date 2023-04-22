<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    const AVAILABLE = 'available';
    const UNAVAILABLE = 'unavailable';
    protected $fillable = [
        'name',
        'description',
        'quantity',
        'status',
        'image',
        'seller_id',
    ];

    protected $hidden = [
        'pivot',
    ];
    public function isAvailable(){

        return $this->status == Product::AVAILABLE;
    }

    public function seller(){

        return $this->belongsTo(Seller::class);
    }
    public function categories(){

        return $this->belongsToMany(Category::class);
    }

    public function transactions(){

        return $this->hasMany(Transaction::class);
    }

}
