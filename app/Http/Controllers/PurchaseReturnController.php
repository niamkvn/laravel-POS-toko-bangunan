<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Services\InventoryService;
use App\Services\NumberingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseReturnController extends Controller
{
    public function __construct(private InventoryService $inventory, private NumberingService $numbering) {}

    public function index(Request $request)
    {
        $returns = PurchaseReturn::with('purchase', 'supplier')
            ->when($request->search, fn ($q) => $q->where('return_number', 'like', "%{$request->search}%"))
            ->latest()->paginate(15)->withQueryString();

        return view('purchase-returns.index', compact('returns'));
    }

    public function create(Request $request)
    {
        $purchase = null;
        if ($request->purchase_number) {
            $purchase = Purchase::with('items.product', 'items.unit')
                ->where('purchase_number', $request->purchase_number)->first();
            if (! $purchase) {
                return redirect()->route('purchase-returns.create')->with('error', 'No. Pembelian tidak ditemukan.');
            }
        }

        return view('purchase-returns.create', compact('purchase'));
    }

    public function returnableItems(Purchase $purchase)
    {
        $purchase->load('items.product', 'items.unit');

        return response()->json($purchase->items->filter(fn ($i) => $i->returnableQty() > 0)->values());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'reason' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.purchase_item_id' => 'required|exists:purchase_items,id',
            'items.*.qty' => 'required|numeric|min:0.001',
        ]);

        $purchase = Purchase::with('items', 'warehouse')->findOrFail($data['purchase_id']);

        $purchaseReturn = DB::transaction(function () use ($data, $purchase) {
            $return = PurchaseReturn::create([
                'return_number' => $this->numbering->next('RETB'),
                'purchase_id' => $purchase->id,
                'supplier_id' => $purchase->supplier_id,
                'warehouse_id' => $purchase->warehouse_id,
                'return_date' => now(),
                'reason' => $data['reason'] ?? null,
                'status' => 'completed',
                'user_id' => auth()->id(),
            ]);

            $total = 0;
            foreach ($data['items'] as $itemData) {
                $purchaseItem = $purchase->items->firstWhere('id', $itemData['purchase_item_id']);
                if (! $purchaseItem || $itemData['qty'] > $purchaseItem->returnableQty() + 0.001) {
                    throw ValidationException::withMessages(['items' => 'Qty retur melebihi qty yang dapat diretur.']);
                }

                $productUnit = \App\Models\ProductUnit::where('product_id', $purchaseItem->product_id)->where('unit_id', $purchaseItem->unit_id)->first();
                $baseQty = $productUnit ? $productUnit->toBaseQty($itemData['qty']) : $itemData['qty'];
                $subtotal = $itemData['qty'] * $purchaseItem->unit_cost;

                $return->items()->create([
                    'purchase_item_id' => $purchaseItem->id,
                    'product_id' => $purchaseItem->product_id,
                    'unit_id' => $purchaseItem->unit_id,
                    'qty' => $itemData['qty'],
                    'base_qty' => $baseQty,
                    'unit_cost' => $purchaseItem->unit_cost,
                    'subtotal' => $subtotal,
                ]);

                $purchaseItem->increment('returned_qty', $itemData['qty']);
                $this->inventory->purchaseReturnOut($purchaseItem->product, $purchase->warehouse, $baseQty, (float) $purchaseItem->unit_cost, $return, "Retur {$return->return_number}");

                $total += $subtotal;
            }

            $return->update(['total' => $total]);
            $purchase->decrement('total', $total);
            if ($purchase->paid_amount > $purchase->total) {
                $purchase->update(['paid_amount' => $purchase->total]);
            }
            $purchase->update(['status' => $purchase->outstanding() <= 0.01 ? 'paid' : ($purchase->paid_amount > 0 ? 'partial' : 'unpaid')]);

            AuditLog::create([
                'user_id' => auth()->id(), 'action' => 'create', 'module' => 'purchase_return',
                'record_type' => PurchaseReturn::class, 'record_id' => $return->id,
                'new_values' => $return->toArray(),
                'ip_address' => request()->ip(), 'user_agent' => request()->userAgent(),
            ]);

            return $return;
        });

        return redirect()->route('purchase-returns.show', $purchaseReturn)->with('success', 'Retur pembelian berhasil diproses.');
    }

    public function show(PurchaseReturn $purchaseReturn)
    {
        $purchaseReturn->load('items.product', 'items.unit', 'purchase', 'supplier');

        return view('purchase-returns.show', compact('purchaseReturn'));
    }
}
