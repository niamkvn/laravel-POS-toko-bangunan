<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerPrice;
use App\Models\PriceLevel;
use App\Models\Product;
use App\Models\ProductPrice;

class PricingService
{
    /**
     * Resolve the unit price for a product given an optional customer, quantity, and
     * optional explicit price level (used by POS when a walk-in price level is chosen
     * without a saved customer). Priority: Customer Specific > Quantity Tier > Customer
     * Type / Price Level > Default Price.
     */
    public function priceFor(Product $product, ?Customer $customer, float $qty = 1, ?PriceLevel $priceLevel = null): array
    {
        if ($customer) {
            $customerPrice = CustomerPrice::where('customer_id', $customer->id)
                ->where('product_id', $product->id)
                ->where('active', true)
                ->where('min_qty', '<=', $qty)
                ->orderByDesc('min_qty')
                ->first();

            if ($customerPrice) {
                return ['price' => (float) $customerPrice->price, 'source' => 'customer_specific'];
            }
        }

        $quantityTier = ProductPrice::where('product_id', $product->id)
            ->whereNull('price_level_id')
            ->where('active', true)
            ->where('min_qty', '<=', $qty)
            ->orderByDesc('min_qty')
            ->first();

        if ($quantityTier) {
            return ['price' => (float) $quantityTier->price, 'source' => 'quantity_tier'];
        }

        $levelId = $priceLevel?->id ?? $customer?->price_level_id;

        if ($levelId) {
            $levelPrice = ProductPrice::where('product_id', $product->id)
                ->where('price_level_id', $levelId)
                ->where('active', true)
                ->where('min_qty', '<=', $qty)
                ->orderByDesc('min_qty')
                ->first();

            if ($levelPrice) {
                return ['price' => (float) $levelPrice->price, 'source' => 'price_level'];
            }
        }

        return ['price' => (float) $product->default_sell_price, 'source' => 'default'];
    }
}
