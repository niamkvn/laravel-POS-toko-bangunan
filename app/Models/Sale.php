<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'invoice_number', 'sale_date', 'customer_id', 'customer_project_id', 'sales_order_id',
    'warehouse_id', 'price_level_id', 'cashier_shift_id', 'subtotal', 'discount_amount',
    'tax_amount', 'total', 'paid_amount', 'change_amount', 'is_credit', 'due_date',
    'status', 'user_id', 'voided_by', 'void_reason', 'notes',
])]
class Sale extends Model
{
    protected function casts(): array
    {
        return [
            'sale_date' => 'datetime', 'due_date' => 'date', 'is_credit' => 'boolean',
            'subtotal' => 'decimal:2', 'discount_amount' => 'decimal:2', 'tax_amount' => 'decimal:2',
            'total' => 'decimal:2', 'paid_amount' => 'decimal:2', 'change_amount' => 'decimal:2',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function project()
    {
        return $this->belongsTo(CustomerProject::class, 'customer_project_id');
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function priceLevel()
    {
        return $this->belongsTo(PriceLevel::class);
    }

    public function cashierShift()
    {
        return $this->belongsTo(CashierShift::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class);
    }

    public function returns()
    {
        return $this->hasMany(SaleReturn::class);
    }

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class);
    }

    public function outstanding(): float
    {
        return max(0, (float) $this->total - (float) $this->paid_amount);
    }

    public function isOverdue(): bool
    {
        return $this->is_credit && $this->due_date && $this->due_date->isPast() && $this->outstanding() > 0;
    }
}
