<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class InventoryService
{
    /**
     * Core primitive: mutate stock and write a movement record. Must run inside a DB transaction.
     */
    public function recordMovement(
        Product $product,
        Warehouse $warehouse,
        string $movementType,
        float $quantitySigned,
        float $unitCost,
        ?Model $reference = null,
        ?string $notes = null,
    ): StockMovement {
        $stock = Stock::where('product_id', $product->id)
            ->where('warehouse_id', $warehouse->id)
            ->lockForUpdate()
            ->first();

        if (! $stock) {
            $stock = Stock::create([
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'quantity' => 0,
            ]);
        }

        $balanceBefore = (float) $stock->quantity;
        $balanceAfter = $balanceBefore + $quantitySigned;

        if ($balanceAfter < 0 && ! $product->allow_negative_stock) {
            throw new InsufficientStockException("Stok tidak mencukupi untuk produk {$product->name} di gudang {$warehouse->name}. Tersedia: {$balanceBefore}, diminta: ".abs($quantitySigned));
        }

        $stock->update(['quantity' => $balanceAfter]);

        return StockMovement::create([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'movement_type' => $movementType,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id' => $reference?->id,
            'quantity' => $quantitySigned,
            'unit_cost' => $unitCost,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'user_id' => Auth::id(),
            'notes' => $notes,
        ]);
    }

    public function purchaseIn(Product $product, Warehouse $warehouse, float $qty, float $unitCost, ?Model $reference = null, ?string $notes = null): StockMovement
    {
        $currentQty = (float) $product->totalStock();
        $currentAvg = (float) $product->average_cost;
        $newTotalQty = $currentQty + $qty;
        $newAvg = $newTotalQty > 0 ? (($currentAvg * $currentQty) + ($unitCost * $qty)) / $newTotalQty : $unitCost;

        $product->update(['average_cost' => $newAvg, 'purchase_price' => $unitCost]);

        return $this->recordMovement($product, $warehouse, 'PURCHASE', $qty, $unitCost, $reference, $notes);
    }

    public function saleOut(Product $product, Warehouse $warehouse, float $qty, ?Model $reference = null, ?string $notes = null): StockMovement
    {
        return $this->recordMovement($product, $warehouse, 'SALE', -$qty, (float) $product->average_cost, $reference, $notes);
    }

    public function saleReturnIn(Product $product, Warehouse $warehouse, float $qty, float $unitCost, ?Model $reference = null, ?string $notes = null): StockMovement
    {
        return $this->recordMovement($product, $warehouse, 'SALE_RETURN', $qty, $unitCost, $reference, $notes);
    }

    public function purchaseReturnOut(Product $product, Warehouse $warehouse, float $qty, float $unitCost, ?Model $reference = null, ?string $notes = null): StockMovement
    {
        return $this->recordMovement($product, $warehouse, 'PURCHASE_RETURN', -$qty, $unitCost, $reference, $notes);
    }

    public function transferOut(Product $product, Warehouse $warehouse, float $qty, ?Model $reference = null, ?string $notes = null): StockMovement
    {
        return $this->recordMovement($product, $warehouse, 'TRANSFER_OUT', -$qty, (float) $product->average_cost, $reference, $notes);
    }

    public function transferIn(Product $product, Warehouse $warehouse, float $qty, ?Model $reference = null, ?string $notes = null): StockMovement
    {
        return $this->recordMovement($product, $warehouse, 'TRANSFER_IN', $qty, (float) $product->average_cost, $reference, $notes);
    }

    public function opnameAdjust(Product $product, Warehouse $warehouse, float $differenceQty, ?Model $reference = null, ?string $notes = null): StockMovement
    {
        $type = $differenceQty >= 0 ? 'OPNAME_INCREASE' : 'OPNAME_DECREASE';

        return $this->recordMovement($product, $warehouse, $type, $differenceQty, (float) $product->average_cost, $reference, $notes);
    }

    public function adjust(Product $product, Warehouse $warehouse, float $qtySigned, ?Model $reference = null, ?string $notes = null): StockMovement
    {
        $type = $qtySigned >= 0 ? 'ADJUSTMENT_IN' : 'ADJUSTMENT_OUT';

        return $this->recordMovement($product, $warehouse, $type, $qtySigned, (float) $product->average_cost, $reference, $notes);
    }

    public function opening(Product $product, Warehouse $warehouse, float $qty, float $unitCost, ?string $notes = null): StockMovement
    {
        return $this->recordMovement($product, $warehouse, 'OPENING', $qty, $unitCost, null, $notes);
    }
}
