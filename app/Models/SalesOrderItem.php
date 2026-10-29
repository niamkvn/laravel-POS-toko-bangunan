<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['sales_order_id', 'product_id', 'unit_id', 'qty', 'fulfilled_qty', 'price', 'discount_amount', 'subtotal'])]
class SalesOrderItem extends Model
{
    protected function casts(): array
    {
        return [
            'qty' => 'decimal:3', 'fulfilled_qty' => 'decimal:3', 'price' => 'decimal:2',
            'discount_amount' => 'decimal:2', 'subtotal' => 'decimal:2',
        ];
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function remainingQty(): float
    {
        return max(0, (float) $this->qty - (float) $this->fulfilled_qty);
    }
}
