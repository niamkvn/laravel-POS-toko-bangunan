<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'purchase_number', 'supplier_id', 'purchase_order_id', 'goods_receipt_id', 'warehouse_id',
    'purchase_date', 'due_date', 'subtotal', 'discount_amount', 'tax_amount', 'total',
    'paid_amount', 'status', 'created_by',
])]
class Purchase extends Model
{
    protected function casts(): array
    {
        return [
            'purchase_date' => 'date', 'due_date' => 'date',
            'subtotal' => 'decimal:2', 'discount_amount' => 'decimal:2', 'tax_amount' => 'decimal:2',
            'total' => 'decimal:2', 'paid_amount' => 'decimal:2',
        ];
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }

    public function returns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function outstanding(): float
    {
        return max(0, (float) $this->total - (float) $this->paid_amount);
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->outstanding() > 0;
    }
}
