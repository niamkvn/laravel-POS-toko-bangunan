<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['project_code', 'customer_id', 'name', 'location', 'start_date', 'end_date', 'budget', 'status', 'notes'])]
class CustomerProject extends Model
{
    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date', 'budget' => 'decimal:2'];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
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
}
