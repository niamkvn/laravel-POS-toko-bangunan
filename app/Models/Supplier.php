<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'supplier_code', 'name', 'company_name', 'phone', 'email', 'address',
    'tax_number', 'payment_term_days', 'credit_limit', 'notes', 'active',
])]
class Supplier extends Model
{
    protected function casts(): array
    {
        return ['active' => 'boolean', 'credit_limit' => 'decimal:2'];
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function totalPayable(): float
    {
        return (float) $this->purchases()->get()->sum(fn ($p) => max(0, $p->total - $p->paid_amount));
    }
}
