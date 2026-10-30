<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\CashAccount;
use App\Models\CashierShift;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Sale;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalesService
{
    public function __construct(
        private InventoryService $inventory,
        private NumberingService $numbering,
        private PricingService $pricing,
    ) {}

    public function createSale(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $warehouse = Warehouse::findOrFail($data['warehouse_id']);
            $customer = ! empty($data['customer_id']) ? Customer::find($data['customer_id']) : null;
            $status = $data['status'] ?? 'completed';

            $itemRows = [];
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $productUnit = ProductUnit::where('product_id', $product->id)->where('unit_id', $item['unit_id'])->firstOrFail();
                $baseQty = $productUnit->toBaseQty((float) $item['qty']);
                $discount = (float) ($item['discount_amount'] ?? 0);
                $lineSubtotal = ((float) $item['price'] * (float) $item['qty']) - $discount;

                $itemRows[] = [
                    'product' => $product,
                    'unit_id' => $item['unit_id'],
                    'qty' => $item['qty'],
                    'base_qty' => $baseQty,
                    'price' => $item['price'],
                    'unit_cost' => $product->average_cost,
                    'discount_amount' => $discount,
                    'subtotal' => $lineSubtotal,
                ];
                $subtotal += $lineSubtotal;
            }

            $discountAmount = (float) ($data['discount_amount'] ?? 0);
            $taxAmount = (float) ($data['tax_amount'] ?? 0);
            $total = $subtotal - $discountAmount + $taxAmount;
            $payments = $data['payments'] ?? [];
            $paidAmount = collect($payments)->sum('amount');
            $isCredit = $status === 'completed' && $paidAmount < $total - 0.01;
            $changeAmount = $status === 'completed' ? max(0, $paidAmount - $total) : 0;

            if ($isCredit) {
                if (! $customer) {
                    throw ValidationException::withMessages(['customer_id' => 'Penjualan kredit harus memiliki customer.']);
                }
                $outstanding = $customer->totalReceivable();
                $newOutstanding = $outstanding + ($total - $paidAmount);
                if ($customer->credit_limit > 0 && $newOutstanding > $customer->credit_limit) {
                    throw ValidationException::withMessages(['customer_id' => "Limit kredit customer terlampaui. Limit: Rp ".number_format($customer->credit_limit, 0, ',', '.').", akan menjadi: Rp ".number_format($newOutstanding, 0, ',', '.')]);
                }
            }

            $sale = Sale::create([
                'invoice_number' => $this->numbering->next('INV'),
                'sale_date' => now(),
                'customer_id' => $customer?->id,
                'customer_project_id' => $data['customer_project_id'] ?? null,
                'sales_order_id' => $data['sales_order_id'] ?? null,
                'warehouse_id' => $warehouse->id,
                'price_level_id' => $data['price_level_id'] ?? null,
                'cashier_shift_id' => $data['cashier_shift_id'] ?? null,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'paid_amount' => $status === 'completed' ? min($paidAmount, $total) : 0,
                'change_amount' => $changeAmount,
                'is_credit' => $isCredit,
                'due_date' => $isCredit ? now()->addDays($customer->payment_term_days ?: 30) : null,
                'status' => $status,
                'user_id' => auth()->id(),
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($itemRows as $row) {
                $saleItem = $sale->items()->create([
                    'product_id' => $row['product']->id,
                    'unit_id' => $row['unit_id'],
                    'qty' => $row['qty'],
                    'base_qty' => $row['base_qty'],
                    'price' => $row['price'],
                    'unit_cost' => $row['unit_cost'],
                    'discount_amount' => $row['discount_amount'],
                    'subtotal' => $row['subtotal'],
                ]);

                if ($status === 'completed') {
                    $this->inventory->saleOut($row['product'], $warehouse, $row['base_qty'], $sale, "Penjualan {$sale->invoice_number}");
                }
            }

            if ($status === 'completed') {
                foreach ($payments as $payment) {
                    $sale->payments()->create([
                        'payment_method_id' => $payment['payment_method_id'],
                        'amount' => $payment['amount'],
                        'reference_number' => $payment['reference_number'] ?? null,
                        'paid_at' => now(),
                        'user_id' => auth()->id(),
                    ]);
                }

                if (! empty($data['cashier_shift_id'])) {
                    $shift = CashierShift::find($data['cashier_shift_id']);
                    $cashPaid = collect($payments)->filter(function ($p) {
                        $method = \App\Models\PaymentMethod::find($p['payment_method_id']);

                        return $method && $method->type === 'cash';
                    })->sum('amount');

                    if ($shift && $cashPaid > 0) {
                        $shift->increment('cash_sales', min($cashPaid, $total));
                        $shift->cashAccount?->increment('current_balance', min($cashPaid, $total));
                        $shift->cashAccount?->transactions()->create([
                            'type' => 'in',
                            'amount' => min($cashPaid, $total),
                            'reference_type' => Sale::class,
                            'reference_id' => $sale->id,
                            'category' => 'sales',
                            'description' => "Penjualan tunai {$sale->invoice_number}",
                            'transaction_date' => now(),
                            'user_id' => auth()->id(),
                        ]);
                    }
                }
            }

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => $status === 'completed' ? 'create' : 'hold',
                'module' => 'sales',
                'record_type' => Sale::class,
                'record_id' => $sale->id,
                'new_values' => $sale->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $sale;
        });
    }

    public function voidSale(Sale $sale, string $reason): Sale
    {
        return DB::transaction(function () use ($sale, $reason) {
            if ($sale->status === 'void') {
                throw ValidationException::withMessages(['status' => 'Transaksi sudah void.']);
            }

            if ($sale->status === 'completed') {
                foreach ($sale->items as $item) {
                    $this->inventory->saleReturnIn($item->product, $sale->warehouse, (float) $item->base_qty, (float) $item->unit_cost, $sale, "Void {$sale->invoice_number}");
                }
            }

            $sale->update([
                'status' => 'void',
                'voided_by' => auth()->id(),
                'void_reason' => $reason,
            ]);

            AuditLog::create([
                'user_id' => auth()->id(), 'action' => 'void', 'module' => 'sales',
                'record_type' => Sale::class, 'record_id' => $sale->id,
                'old_values' => ['status' => 'completed'], 'new_values' => ['status' => 'void', 'reason' => $reason],
                'ip_address' => request()->ip(), 'user_agent' => request()->userAgent(),
            ]);

            return $sale;
        });
    }
}
