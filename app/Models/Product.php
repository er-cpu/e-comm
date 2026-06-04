<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'discount_percent',
        'stock',
        'image',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    public function wishlistItems()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function ratings()
    {
        return $this->hasMany(ProductRating::class);
    }

    public function currentPrice(): float
    {
        if ($this->discount_percent > 0) {
            return round($this->price * (100 - $this->discount_percent) / 100, 2);
        }
        return $this->price;
    }

    public function hasDiscount(): bool
    {
        return $this->discount_percent > 0;
    }
}
