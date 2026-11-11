<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['stock_opname_id', 'product_id', 'system_qty', 'physical_qty', 'difference_qty', 'unit_cost', 'difference_value', 'reason', 'notes'])]
class StockOpnameItem extends Model
{
    protected function casts(): array
    {
        return [
            'system_qty' => 'decimal:3', 'physical_qty' => 'decimal:3', 'difference_qty' => 'decimal:3',
            'unit_cost' => 'decimal:2', 'difference_value' => 'decimal:2',
        ];
    }

    public function stockOpname()
    {
        return $this->belongsTo(StockOpname::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
