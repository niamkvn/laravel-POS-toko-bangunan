<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Services\InventoryService;
use App\Services\NumberingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleReturnController extends Controller
{
    public function __construct(private InventoryService $inventory, private NumberingService $numbering) {}

    public function index(Request $request)
    {
        $returns = SaleReturn::with('sale', 'customer')
            ->when($request->search, fn ($q) => $q->where('return_number', 'like', "%{$request->search}%"))
            ->latest()->paginate(15)->withQueryString();

        return view('sale-returns.index', compact('returns'));
    }

    public function create(Request $request)
    {
        $sale = null;
        if ($request->invoice) {
            $sale = Sale::with('items.product', 'items.unit')
                ->where('invoice_number', $request->invoice)
                ->where('status', 'completed')
                ->first();
            if (! $sale) {
                return redirect()->route('sale-returns.create')->with('error', 'Invoice tidak ditemukan atau belum completed.');
            }
        }

        return view('sale-returns.create', compact('sale'));
    }

    public function returnableItems(Sale $sale)
    {
        $sale->load('items.product', 'items.unit');

        return response()->json($sale->items->filter(fn ($i) => $i->returnableQty() > 0)->map(fn ($i) => [
            'sale_item_id' => $i->id,
            'product_name' => $i->product->name,
            'unit_code' => $i->unit->code,
            'unit_id' => $i->unit_id,
            'returnable_qty' => $i->returnableQty(),
            'price' => (float) $i->price,
        ])->values());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'reason' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.sale_item_id' => 'required|exists:sale_items,id',
            'items.*.qty' => 'required|numeric|min:0.001',
        ]);

        $sale = Sale::with('items', 'warehouse')->findOrFail($data['sale_id']);

        $saleReturn = DB::transaction(function () use ($data, $sale) {
            $return = SaleReturn::create([
                'return_number' => $this->numbering->next('RETJ'),
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'warehouse_id' => $sale->warehouse_id,
                'return_date' => now(),
                'reason' => $data['reason'] ?? null,
                'status' => 'completed',
                'user_id' => auth()->id(),
            ]);

            $total = 0;
            foreach ($data['items'] as $itemData) {
                $saleItem = $sale->items->firstWhere('id', $itemData['sale_item_id']);
                if (! $saleItem || $itemData['qty'] > $saleItem->returnableQty() + 0.001) {
                    throw ValidationException::withMessages(['items' => 'Qty retur melebihi qty yang dapat diretur.']);
                }

                $productUnit = \App\Models\ProductUnit::where('product_id', $saleItem->product_id)->where('unit_id', $saleItem->unit_id)->first();
                $baseQty = $productUnit ? $productUnit->toBaseQty($itemData['qty']) : $itemData['qty'];
                $subtotal = $itemData['qty'] * $saleItem->price;

                $return->items()->create([
                    'sale_item_id' => $saleItem->id,
                    'product_id' => $saleItem->product_id,
                    'unit_id' => $saleItem->unit_id,
                    'qty' => $itemData['qty'],
                    'base_qty' => $baseQty,
                    'price' => $saleItem->price,
                    'subtotal' => $subtotal,
                ]);

                $saleItem->increment('returned_qty', $itemData['qty']);
                $this->inventory->saleReturnIn($saleItem->product, $sale->warehouse, $baseQty, (float) $saleItem->unit_cost, $return, "Retur {$return->return_number}");

                $total += $subtotal;
            }

            $return->update(['total' => $total]);
            $sale->decrement('total', $total);

            AuditLog::create([
                'user_id' => auth()->id(), 'action' => 'create', 'module' => 'sale_return',
                'record_type' => SaleReturn::class, 'record_id' => $return->id,
                'new_values' => $return->toArray(),
                'ip_address' => request()->ip(), 'user_agent' => request()->userAgent(),
            ]);

            return $return;
        });

        return redirect()->route('sale-returns.show', $saleReturn)->with('success', 'Retur penjualan berhasil diproses.');
    }

    public function show(SaleReturn $saleReturn)
    {
        $saleReturn->load('items.product', 'items.unit', 'sale', 'customer');

        return view('sale-returns.show', compact('saleReturn'));
    }
}
