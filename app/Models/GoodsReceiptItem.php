<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['goods_receipt_id', 'purchase_order_item_id', 'product_id', 'unit_id', 'qty', 'base_qty', 'damaged_qty', 'unit_cost', 'subtotal'])]
class GoodsReceiptItem extends Model
{
    protected function casts(): array
    {
        return [
            'qty' => 'decimal:3', 'base_qty' => 'decimal:3', 'damaged_qty' => 'decimal:3',
            'unit_cost' => 'decimal:2', 'subtotal' => 'decimal:2',
        ];
    }

    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
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
