<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['sale_return_id', 'sale_item_id', 'product_id', 'unit_id', 'qty', 'base_qty', 'price', 'subtotal'])]
class SaleReturnItem extends Model
{
    protected function casts(): array
    {
        return ['qty' => 'decimal:3', 'base_qty' => 'decimal:3', 'price' => 'decimal:2', 'subtotal' => 'decimal:2'];
    }

    public function saleReturn()
    {
        return $this->belongsTo(SaleReturn::class);
    }

    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class);
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
