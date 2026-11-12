<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['stock_adjustment_id', 'product_id', 'adjustment_qty', 'unit_cost', 'notes'])]
class StockAdjustmentItem extends Model
{
    protected function casts(): array
    {
        return ['adjustment_qty' => 'decimal:3', 'unit_cost' => 'decimal:2'];
    }

    public function stockAdjustment()
    {
        return $this->belongsTo(StockAdjustment::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
