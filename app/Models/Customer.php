<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'customer_code', 'name', 'company_name', 'phone', 'email', 'address',
    'customer_type', 'price_level_id', 'credit_limit', 'payment_term_days',
    'tax_number', 'notes', 'active',
])]
class Customer extends Model
{
    protected function casts(): array
    {
        return ['active' => 'boolean', 'credit_limit' => 'decimal:2'];
    }

    public function priceLevel()
    {
        return $this->belongsTo(PriceLevel::class);
    }

    public function specialPrices()
    {
        return $this->hasMany(CustomerPrice::class);
    }

    public function projects()
    {
        return $this->hasMany(CustomerProject::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class);
    }

    public function totalReceivable(): float
    {
        return (float) $this->sales()
            ->where('is_credit', true)
            ->where('status', '!=', 'void')
            ->get()
            ->sum(fn ($sale) => max(0, $sale->total - $sale->paid_amount));
    }
}
