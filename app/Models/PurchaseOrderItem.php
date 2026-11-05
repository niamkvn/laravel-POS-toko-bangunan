<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['purchase_order_id', 'product_id', 'unit_id', 'qty', 'received_qty', 'price', 'subtotal'])]
class PurchaseOrderItem extends Model
{
    protected function casts(): array
    {
        return ['qty' => 'decimal:3', 'received_qty' => 'decimal:3', 'price' => 'decimal:2', 'subtotal' => 'decimal:2'];
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
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
        return max(0, (float) $this->qty - (float) $this->received_qty);
    }
}
