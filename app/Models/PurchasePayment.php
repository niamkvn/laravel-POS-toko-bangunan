<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['purchase_id', 'cash_account_id', 'amount', 'payment_date', 'reference_number', 'user_id', 'notes'])]
class PurchasePayment extends Model
{
    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'payment_date' => 'date'];
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
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
