<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['quotation_id', 'product_id', 'unit_id', 'qty', 'price', 'discount_amount', 'subtotal'])]
class QuotationItem extends Model
{
    protected function casts(): array
    {
        return ['qty' => 'decimal:3', 'price' => 'decimal:2', 'discount_amount' => 'decimal:2', 'subtotal' => 'decimal:2'];
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
