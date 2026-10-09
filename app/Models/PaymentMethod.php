<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['code', 'name', 'type', 'account_number', 'active'])]
class PaymentMethod extends Model
{
    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function salePayments()
    {
        return $this->hasMany(SalePayment::class);
    }
}
