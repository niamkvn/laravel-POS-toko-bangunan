<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\ProductUnit;
use App\Models\StockTransfer;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockTransferService
{
    public function __construct(private InventoryService $inventory, private NumberingService $numbering) {}

    public function create(array $data): StockTransfer
    {
        return DB::transaction(function () use ($data) {
            if ($data['source_warehouse_id'] == $data['destination_warehouse_id']) {
                throw ValidationException::withMessages(['destination_warehouse_id' => 'Gudang tujuan harus berbeda dengan gudang asal.']);
            }

            $transfer = StockTransfer::create([
                'transfer_number' => $this->numbering->next('TRF'),
                'source_warehouse_id' => $data['source_warehouse_id'],
                'destination_warehouse_id' => $data['destination_warehouse_id'],
                'transfer_date' => $data['transfer_date'] ?? now(),
                'status' => 'draft',
                'requested_by' => auth()->id(),
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $productUnit = ProductUnit::where('product_id', $item['product_id'])->where('unit_id', $item['unit_id'])->firstOrFail();
                $transfer->items()->create([
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'qty' => $item['qty'],
                    'base_qty' => $productUnit->toBaseQty((float) $item['qty']),
                ]);
            }

            AuditLog::create([
                'user_id' => auth()->id(), 'action' => 'create', 'module' => 'stock_transfer',
                'record_type' => StockTransfer::class, 'record_id' => $transfer->id,
                'new_values' => $transfer->toArray(),
                'ip_address' => request()->ip(), 'user_agent' => request()->userAgent(),
            ]);

            return $transfer;
        });
    }

    public function approve(StockTransfer $transfer): StockTransfer
    {
        $transfer->update(['status' => 'approved', 'approved_by' => auth()->id()]);

        return $transfer;
    }

    public function ship(StockTransfer $transfer): StockTransfer
    {
        return DB::transaction(function () use ($transfer) {
            $transfer->load('items.product', 'sourceWarehouse');

            foreach ($transfer->items as $item) {
                $this->inventory->transferOut($item->product, $transfer->sourceWarehouse, (float) $item->base_qty, $transfer, "Transfer keluar {$transfer->transfer_number}");
            }

            $transfer->update(['status' => 'shipped']);

            AuditLog::create([
                'user_id' => auth()->id(), 'action' => 'ship', 'module' => 'stock_transfer',
                'record_type' => StockTransfer::class, 'record_id' => $transfer->id,
                'ip_address' => request()->ip(), 'user_agent' => request()->userAgent(),
            ]);

            return $transfer;
        });
    }

    public function receive(StockTransfer $transfer): StockTransfer
    {
        return DB::transaction(function () use ($transfer) {
            $transfer->load('items.product', 'destinationWarehouse');

            foreach ($transfer->items as $item) {
                $this->inventory->transferIn($item->product, $transfer->destinationWarehouse, (float) $item->base_qty, $transfer, "Transfer masuk {$transfer->transfer_number}");
                $item->update(['received_qty' => $item->qty]);
            }

            $transfer->update(['status' => 'received']);

            AuditLog::create([
                'user_id' => auth()->id(), 'action' => 'receive', 'module' => 'stock_transfer',
                'record_type' => StockTransfer::class, 'record_id' => $transfer->id,
                'ip_address' => request()->ip(), 'user_agent' => request()->userAgent(),
            ]);

            return $transfer;
        });
    }
}
