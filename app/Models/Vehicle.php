<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['vehicle_code', 'plate_number', 'vehicle_type', 'capacity', 'driver_id', 'active'])]
class Vehicle extends Model
{
    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class);
    }
}
