<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['code', 'name', 'type', 'account_number', 'opening_balance', 'current_balance', 'active'])]
class CashAccount extends Model
{
    protected function casts(): array
    {
        return ['active' => 'boolean', 'opening_balance' => 'decimal:2', 'current_balance' => 'decimal:2'];
    }

    public function transactions()
    {
        return $this->hasMany(CashTransaction::class);
    }
}
