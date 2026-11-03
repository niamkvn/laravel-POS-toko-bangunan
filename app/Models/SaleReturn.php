<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['return_number', 'sale_id', 'customer_id', 'warehouse_id', 'return_date', 'reason', 'total', 'status', 'user_id'])]
class SaleReturn extends Model
{
    protected function casts(): array
    {
        return ['return_date' => 'date', 'total' => 'decimal:2'];
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(SaleReturnItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
