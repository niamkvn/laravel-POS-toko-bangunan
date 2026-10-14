<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'sku', 'barcode', 'product_code', 'name', 'category_id', 'brand_id', 'unit_id',
    'description', 'purchase_price', 'default_sell_price', 'average_cost',
    'minimum_stock', 'maximum_stock', 'tax_id', 'image', 'allow_negative_stock',
    'active', 'created_by', 'updated_by',
])]
class Product extends Model
{
    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'allow_negative_stock' => 'boolean',
            'purchase_price' => 'decimal:2',
            'default_sell_price' => 'decimal:2',
            'average_cost' => 'decimal:2',
            'minimum_stock' => 'decimal:3',
            'maximum_stock' => 'decimal:3',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function baseUnit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function units()
    {
        return $this->hasMany(ProductUnit::class);
    }

    public function prices()
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function customerPrices()
    {
        return $this->hasMany(CustomerPrice::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function totalStock(): float
    {
        return (float) $this->stocks()->sum('quantity');
    }

    public function stockInWarehouse(int $warehouseId): float
    {
        return (float) $this->stocks()->where('warehouse_id', $warehouseId)->value('quantity') ?? 0;
    }

    public function isLowStock(): bool
    {
        return $this->totalStock() <= (float) $this->minimum_stock;
    }
}
