<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['purchase_id', 'product_id', 'unit_id', 'qty', 'base_qty', 'unit_cost', 'subtotal', 'returned_qty'])]
class PurchaseItem extends Model
{
    protected function casts(): array
    {
        return [
            'qty' => 'decimal:3', 'base_qty' => 'decimal:3', 'unit_cost' => 'decimal:2',
            'subtotal' => 'decimal:2', 'returned_qty' => 'decimal:3',
        ];
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function returnableQty(): float
    {
        return max(0, (float) $this->qty - (float) $this->returned_qty);
    }
}
