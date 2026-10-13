<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['code', 'name', 'description', 'is_default'])]
class PriceLevel extends Model
{
    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    public function productPrices()
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
