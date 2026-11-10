<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['stock_transfer_id', 'product_id', 'unit_id', 'qty', 'base_qty', 'received_qty'])]
class StockTransferItem extends Model
{
    protected function casts(): array
    {
        return ['qty' => 'decimal:3', 'base_qty' => 'decimal:3', 'received_qty' => 'decimal:3'];
    }

    public function stockTransfer()
    {
        return $this->belongsTo(StockTransfer::class);
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
