<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'user_id', 'cash_account_id', 'opening_balance', 'cash_sales', 'cash_in', 'cash_out',
    'expected_cash', 'actual_cash', 'difference', 'status', 'opened_at', 'closed_at', 'notes',
])]
class CashierShift extends Model
{
    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:2',
            'cash_sales' => 'decimal:2',
            'cash_in' => 'decimal:2',
            'cash_out' => 'decimal:2',
            'expected_cash' => 'decimal:2',
            'actual_cash' => 'decimal:2',
            'difference' => 'decimal:2',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cashAccount()
    {
        return $this->belongsTo(CashAccount::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
