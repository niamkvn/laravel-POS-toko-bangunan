<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['product_id', 'unit_id', 'conversion_factor', 'is_base', 'is_purchase_unit', 'is_sale_unit', 'barcode'])]
class ProductUnit extends Model
{
    protected function casts(): array
    {
        return [
            'conversion_factor' => 'decimal:4',
            'is_base' => 'boolean',
            'is_purchase_unit' => 'boolean',
            'is_sale_unit' => 'boolean',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function toBaseQty(float $qty): float
    {
        return $qty * (float) $this->conversion_factor;
    }
}
