<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'number', 'customer_id', 'customer_project_id', 'date', 'valid_until',
    'subtotal', 'discount_amount', 'tax_amount', 'total', 'notes', 'status', 'created_by',
])]
class Quotation extends Model
{
    protected function casts(): array
    {
        return [
            'date' => 'date', 'valid_until' => 'date',
            'subtotal' => 'decimal:2', 'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2', 'total' => 'decimal:2',
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

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function salesOrder()
    {
        return $this->hasOne(SalesOrder::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
