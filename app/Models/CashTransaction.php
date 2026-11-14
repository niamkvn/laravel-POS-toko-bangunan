<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['cash_account_id', 'type', 'amount', 'reference_type', 'reference_id', 'category', 'description', 'transaction_date', 'user_id'])]
class CashTransaction extends Model
{
    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'transaction_date' => 'date'];
    }

    public function cashAccount()
    {
        return $this->belongsTo(CashAccount::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
