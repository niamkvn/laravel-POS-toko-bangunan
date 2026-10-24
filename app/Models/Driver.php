<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['driver_code', 'name', 'phone', 'license_number', 'active'])]
class Driver extends Model
{
    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class);
    }
}
