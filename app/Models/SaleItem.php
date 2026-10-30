<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['sale_id', 'product_id', 'unit_id', 'qty', 'base_qty', 'price', 'unit_cost', 'discount_amount', 'subtotal', 'returned_qty'])]
class SaleItem extends Model
{
    protected function casts(): array
    {
        return [
            'qty' => 'decimal:3', 'base_qty' => 'decimal:3', 'price' => 'decimal:2',
            'unit_cost' => 'decimal:2', 'discount_amount' => 'decimal:2', 'subtotal' => 'decimal:2',
            'returned_qty' => 'decimal:3',
        ];
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function grossProfit(): float
    {
        return ((float) $this->price - (float) $this->unit_cost) * (float) $this->qty - (float) $this->discount_amount;
    }

    public function returnableQty(): float
    {
        return max(0, (float) $this->qty - (float) $this->returned_qty);
    }
}
