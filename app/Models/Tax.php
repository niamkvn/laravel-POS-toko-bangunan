<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'rate', 'is_default', 'active'])]
class Tax extends Model
{
    protected function casts(): array
    {
        return ['is_default' => 'boolean', 'active' => 'boolean', 'rate' => 'decimal:2'];
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
