<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'active'])]
class ExpenseCategory extends Model
{
    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
