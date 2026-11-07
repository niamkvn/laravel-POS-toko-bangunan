<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseOrder;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseService
{
    public function __construct(private InventoryService $inventory, private NumberingService $numbering) {}

    public function createPurchaseOrder(array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($data) {
            $subtotal = collect($data['items'])->sum(fn ($i) => $i['qty'] * $i['price']);

            $po = PurchaseOrder::create([
                'po_number' => $this->numbering->next('PO'),
                'supplier_id' => $data['supplier_id'],
                'warehouse_id' => $data['warehouse_id'],
                'order_date' => $data['order_date'] ?? now(),
                'expected_date' => $data['expected_date'] ?? null,
                'status' => 'draft',
                'subtotal' => $subtotal,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'tax_amount' => $data['tax_amount'] ?? 0,
                'total' => $subtotal - ($data['discount_amount'] ?? 0) + ($data['tax_amount'] ?? 0),
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($data['items'] as $item) {
                $po->items()->create([
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['qty'] * $item['price'],
                ]);
            }

            AuditLog::create([
                'user_id' => auth()->id(), 'action' => 'create', 'module' => 'purchase_order',
                'record_type' => PurchaseOrder::class, 'record_id' => $po->id,
                'new_values' => $po->toArray(),
                'ip_address' => request()->ip(), 'user_agent' => request()->userAgent(),
            ]);

            return $po;
        });
    }

    public function receiveGoods(array $data): GoodsReceipt
    {
        return DB::transaction(function () use ($data) {
            $warehouse = Warehouse::findOrFail($data['warehouse_id']);
            $po = ! empty($data['purchase_order_id']) ? PurchaseOrder::find($data['purchase_order_id']) : null;

            $receipt = GoodsReceipt::create([
                'receipt_number' => $this->numbering->next('GR'),
                'purchase_order_id' => $po?->id,
                'supplier_id' => $data['supplier_id'],
                'warehouse_id' => $warehouse->id,
                'receipt_date' => now(),
                'receiver_id' => auth()->id(),
                'notes' => $data['notes'] ?? null,
                'status' => 'posted',
            ]);

            $subtotal = 0;
            $purchaseItems = [];

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $productUnit = \App\Models\ProductUnit::where('product_id', $product->id)->where('unit_id', $item['unit_id'])->firstOrFail();
                $baseQty = $productUnit->toBaseQty((float) $item['qty']);
                $unitCost = (float) $item['unit_cost'];
                $damagedQty = (float) ($item['damaged_qty'] ?? 0);
                $goodQty = $baseQty - $productUnit->toBaseQty($damagedQty);

                $receipt->items()->create([
                    'purchase_order_item_id' => $item['purchase_order_item_id'] ?? null,
                    'product_id' => $product->id,
                    'unit_id' => $item['unit_id'],
                    'qty' => $item['qty'],
                    'base_qty' => $baseQty,
                    'damaged_qty' => $damagedQty,
                    'unit_cost' => $unitCost,
                    'subtotal' => $item['qty'] * $unitCost,
                ]);

                if ($goodQty > 0) {
                    $this->inventory->purchaseIn($product, $warehouse, $goodQty, $unitCost, $receipt, "Penerimaan {$receipt->receipt_number}");
                }

                if (! empty($item['purchase_order_item_id'])) {
                    $poItem = \App\Models\PurchaseOrderItem::find($item['purchase_order_item_id']);
                    $poItem?->increment('received_qty', $item['qty']);
                }

                $subtotal += $item['qty'] * $unitCost;
                $purchaseItems[] = [
                    'product_id' => $product->id, 'unit_id' => $item['unit_id'],
                    'qty' => $item['qty'], 'base_qty' => $baseQty, 'unit_cost' => $unitCost,
                    'subtotal' => $item['qty'] * $unitCost,
                ];
            }

            if ($po) {
                $allReceived = $po->items->every(fn ($i) => $i->fresh()->received_qty >= $i->qty);
                $po->update(['status' => $allReceived ? 'received' : 'partially_received']);
            }

            $purchase = Purchase::create([
                'purchase_number' => $this->numbering->next('PB'),
                'supplier_id' => $data['supplier_id'],
                'purchase_order_id' => $po?->id,
                'goods_receipt_id' => $receipt->id,
                'warehouse_id' => $warehouse->id,
                'purchase_date' => now(),
                'due_date' => now()->addDays(\App\Models\Supplier::find($data['supplier_id'])->payment_term_days ?: 30),
                'subtotal' => $subtotal,
                'discount_amount' => 0,
                'tax_amount' => 0,
                'total' => $subtotal,
                'paid_amount' => 0,
                'status' => 'unpaid',
                'created_by' => auth()->id(),
            ]);

            foreach ($purchaseItems as $pi) {
                $purchase->items()->create($pi);
            }

            AuditLog::create([
                'user_id' => auth()->id(), 'action' => 'create', 'module' => 'goods_receipt',
                'record_type' => GoodsReceipt::class, 'record_id' => $receipt->id,
                'new_values' => $receipt->toArray(),
                'ip_address' => request()->ip(), 'user_agent' => request()->userAgent(),
            ]);

            return $receipt;
        });
    }

    public function payPurchase(Purchase $purchase, float $amount, int $cashAccountId, ?string $referenceNumber = null, ?string $notes = null): Purchase
    {
        return DB::transaction(function () use ($purchase, $amount, $cashAccountId, $referenceNumber, $notes) {
            if ($amount > $purchase->outstanding() + 0.01) {
                throw ValidationException::withMessages(['amount' => 'Jumlah pembayaran melebihi sisa hutang.']);
            }

            $purchase->payments()->create([
                'cash_account_id' => $cashAccountId,
                'amount' => $amount,
                'payment_date' => now(),
                'reference_number' => $referenceNumber,
                'user_id' => auth()->id(),
                'notes' => $notes,
            ]);

            $purchase->increment('paid_amount', $amount);
            $purchase->update(['status' => $purchase->outstanding() <= 0.01 ? 'paid' : 'partial']);

            $cashAccount = \App\Models\CashAccount::find($cashAccountId);
            $cashAccount->decrement('current_balance', $amount);
            $cashAccount->transactions()->create([
                'type' => 'out',
                'amount' => $amount,
                'reference_type' => Purchase::class,
                'reference_id' => $purchase->id,
                'category' => 'payment_payable',
                'description' => "Pembayaran hutang {$purchase->purchase_number}",
                'transaction_date' => now(),
                'user_id' => auth()->id(),
            ]);

            AuditLog::create([
                'user_id' => auth()->id(), 'action' => 'payment', 'module' => 'payable',
                'record_type' => Purchase::class, 'record_id' => $purchase->id,
                'new_values' => ['amount' => $amount],
                'ip_address' => request()->ip(), 'user_agent' => request()->userAgent(),
            ]);

            return $purchase;
        });
    }
}
