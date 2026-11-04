<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['do_number', 'sale_id', 'customer_id', 'delivery_address', 'driver_id', 'vehicle_id', 'delivery_date', 'status', 'proof_of_delivery', 'notes'])]
class DeliveryOrder extends Model
{
    protected function casts(): array
    {
        return ['delivery_date' => 'date'];
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function items()
    {
        return $this->hasMany(DeliveryOrderItem::class);
    }
}
