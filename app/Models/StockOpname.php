<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['opname_number', 'warehouse_id', 'date', 'counter_id', 'reviewer_id', 'approver_id', 'status', 'notes', 'posted_at'])]
class StockOpname extends Model
{
    protected function casts(): array
    {
        return ['date' => 'date', 'posted_at' => 'datetime'];
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(StockOpnameItem::class);
    }

    public function counter()
    {
        return $this->belongsTo(User::class, 'counter_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
