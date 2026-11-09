<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['purchase_return_id', 'purchase_item_id', 'product_id', 'unit_id', 'qty', 'base_qty', 'unit_cost', 'subtotal'])]
class PurchaseReturnItem extends Model
{
    protected function casts(): array
    {
        return ['qty' => 'decimal:3', 'base_qty' => 'decimal:3', 'unit_cost' => 'decimal:2', 'subtotal' => 'decimal:2'];
    }

    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class);
    }

    public function purchaseItem()
    {
        return $this->belongsTo(PurchaseItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
