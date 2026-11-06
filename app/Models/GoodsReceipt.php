<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['receipt_number', 'purchase_order_id', 'supplier_id', 'warehouse_id', 'receipt_date', 'receiver_id', 'notes', 'status'])]
class GoodsReceipt extends Model
{
    protected function casts(): array
    {
        return ['receipt_date' => 'date'];
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function items()
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }
}
