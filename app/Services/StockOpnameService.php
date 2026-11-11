<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Product;
use App\Models\StockOpname;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockOpnameService
{
    public function __construct(private InventoryService $inventory, private NumberingService $numbering) {}

    public function create(int $warehouseId, string $date, ?string $notes = null): StockOpname
    {
        return DB::transaction(function () use ($warehouseId, $date, $notes) {
            $warehouse = Warehouse::findOrFail($warehouseId);

            $opname = StockOpname::create([
                'opname_number' => $this->numbering->next('OP'),
                'warehouse_id' => $warehouse->id,
                'date' => $date,
                'counter_id' => auth()->id(),
                'status' => 'counting',
                'notes' => $notes,
            ]);

            $products = Product::whereHas('stocks', fn ($q) => $q->where('warehouse_id', $warehouse->id))
                ->with(['stocks' => fn ($q) => $q->where('warehouse_id', $warehouse->id)])
                ->where('active', true)
                ->get();

            foreach ($products as $product) {
                $systemQty = (float) ($product->stocks->first()?->quantity ?? 0);
                $opname->items()->create([
                    'product_id' => $product->id,
                    'system_qty' => $systemQty,
                    'unit_cost' => $product->average_cost,
                ]);
            }

            AuditLog::create([
                'user_id' => auth()->id(), 'action' => 'create', 'module' => 'stock_opname',
                'record_type' => StockOpname::class, 'record_id' => $opname->id,
                'ip_address' => request()->ip(), 'user_agent' => request()->userAgent(),
            ]);

            return $opname;
        });
    }

    public function saveCount(StockOpname $opname, array $items): StockOpname
    {
        if (! in_array($opname->status, ['counting', 'review'])) {
            throw ValidationException::withMessages(['status' => 'Opname tidak dalam status counting.']);
        }

        foreach ($items as $itemData) {
            $item = $opname->items()->find($itemData['id']);
            if (! $item) {
                continue;
            }
            $physicalQty = $itemData['physical_qty'] !== null ? (float) $itemData['physical_qty'] : null;
            $diff = $physicalQty !== null ? $physicalQty - (float) $item->system_qty : null;

            $item->update([
                'physical_qty' => $physicalQty,
                'difference_qty' => $diff,
                'difference_value' => $diff !== null ? $diff * (float) $item->unit_cost : null,
                'reason' => $itemData['reason'] ?? null,
                'notes' => $itemData['notes'] ?? null,
            ]);
        }

        return $opname;
    }

    public function submitReview(StockOpname $opname): StockOpname
    {
        $opname->update(['status' => 'review', 'reviewer_id' => auth()->id()]);

        return $opname;
    }

    public function approve(StockOpname $opname): StockOpname
    {
        $opname->update(['status' => 'approved', 'approver_id' => auth()->id()]);

        return $opname;
    }

    public function post(StockOpname $opname): StockOpname
    {
        return DB::transaction(function () use ($opname) {
            if ($opname->status !== 'approved') {
                throw ValidationException::withMessages(['status' => 'Opname harus disetujui sebelum diposting.']);
            }

            $opname->load('items.product', 'warehouse');

            foreach ($opname->items as $item) {
                if ($item->difference_qty === null || abs((float) $item->difference_qty) < 0.0001) {
                    continue;
                }
                $this->inventory->opnameAdjust($item->product, $opname->warehouse, (float) $item->difference_qty, $opname, "Stock Opname {$opname->opname_number}: {$item->reason}");
            }

            $opname->update(['status' => 'posted', 'posted_at' => now()]);

            AuditLog::create([
                'user_id' => auth()->id(), 'action' => 'post', 'module' => 'stock_opname',
                'record_type' => StockOpname::class, 'record_id' => $opname->id,
                'ip_address' => request()->ip(), 'user_agent' => request()->userAgent(),
            ]);

            return $opname;
        });
    }
}
