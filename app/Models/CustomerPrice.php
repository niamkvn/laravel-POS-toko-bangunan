<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['customer_id', 'product_id', 'min_qty', 'price', 'active'])]
class CustomerPrice extends Model
{
    protected function casts(): array
    {
        return ['min_qty' => 'decimal:3', 'price' => 'decimal:2', 'active' => 'boolean'];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
