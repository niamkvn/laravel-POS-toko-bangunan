<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Product;
use App\Models\Setting;
use App\Models\StockAdjustment;
use App\Models\Warehouse;
use App\Services\InventoryService;
use App\Services\NumberingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function __construct(private InventoryService $inventory, private NumberingService $numbering) {}

    public function index(Request $request)
    {
        $adjustments = StockAdjustment::with('warehouse', 'requester')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()->paginate(15)->withQueryString();

        return view('stock-adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        return view('stock-adjustments.form', [
            'warehouses' => Warehouse::where('active', true)->pluck('name', 'id'),
            'products' => Product::where('active', true)->pluck('name', 'id'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'reason' => 'required|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.adjustment_qty' => 'required|numeric|not_in:0',
            'items.*.notes' => 'nullable|string',
        ]);

        $threshold = (float) (\App\Models\ApprovalThreshold::where('code', 'stock_adjustment')->value('threshold_value') ?? 50);
        $totalAbsQty = collect($data['items'])->sum(fn ($i) => abs($i['adjustment_qty']));
        $needsApproval = $totalAbsQty > $threshold;

        $adjustment = DB::transaction(function () use ($data, $needsApproval) {
            $adjustment = StockAdjustment::create([
                'adjustment_number' => $this->numbering->next('ADJ'),
                'warehouse_id' => $data['warehouse_id'],
                'date' => now(),
                'reason' => $data['reason'],
                'status' => $needsApproval ? 'pending' : 'approved',
                'requested_by' => auth()->id(),
            ]);

            foreach ($data['items'] as $item) {
                $product = Product::find($item['product_id']);
                $adjustment->items()->create([
                    'product_id' => $item['product_id'],
                    'adjustment_qty' => $item['adjustment_qty'],
                    'unit_cost' => $product->average_cost,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            AuditLog::create([
                'user_id' => auth()->id(), 'action' => 'create', 'module' => 'stock_adjustment',
                'record_type' => StockAdjustment::class, 'record_id' => $adjustment->id,
                'new_values' => $adjustment->toArray(),
                'ip_address' => request()->ip(), 'user_agent' => request()->userAgent(),
            ]);

            if (! $needsApproval) {
                $this->postAdjustment($adjustment);
            }

            return $adjustment;
        });

        $message = $needsApproval
            ? 'Adjustment melebihi threshold, menunggu approval Manager.'
            : 'Adjustment berhasil diposting dan stok diperbarui.';

        return redirect()->route('stock-adjustments.show', $adjustment)->with('success', $message);
    }

    public function show(StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->load('items.product', 'warehouse', 'requester', 'approver');

        return view('stock-adjustments.show', compact('stockAdjustment'));
    }

    public function approve(StockAdjustment $stockAdjustment)
    {
        if ($stockAdjustment->status !== 'pending') {
            return back()->with('error', 'Adjustment tidak dalam status pending.');
        }

        DB::transaction(function () use ($stockAdjustment) {
            $stockAdjustment->update(['status' => 'approved', 'approved_by' => auth()->id()]);
            $this->postAdjustment($stockAdjustment);
        });

        return back()->with('success', 'Adjustment disetujui dan stok telah diperbarui.');
    }

    private function postAdjustment(StockAdjustment $adjustment): void
    {
        $adjustment->load('items.product', 'warehouse');

        foreach ($adjustment->items as $item) {
            $this->inventory->adjust($item->product, $adjustment->warehouse, (float) $item->adjustment_qty, $adjustment, "Stock Adjustment {$adjustment->adjustment_number}");
        }

        $adjustment->update(['status' => 'posted']);
    }
}
