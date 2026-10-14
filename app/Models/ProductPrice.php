<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['product_id', 'price_level_id', 'min_qty', 'price', 'active'])]
class ProductPrice extends Model
{
    protected function casts(): array
    {
        return ['min_qty' => 'decimal:3', 'price' => 'decimal:2', 'active' => 'boolean'];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function priceLevel()
    {
        return $this->belongsTo(PriceLevel::class);
    }
}
