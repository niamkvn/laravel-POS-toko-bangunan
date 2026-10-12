<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['code', 'name'])]
class Unit extends Model
{
    public function productUnits()
    {
        return $this->hasMany(ProductUnit::class);
    }
}
