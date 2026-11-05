<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\PurchaseService;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function __construct(private PurchaseService $purchaseService) {}

    public function index(Request $request)
    {
        $orders = PurchaseOrder::with('supplier')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->search, fn ($q) => $q->where('po_number', 'like', "%{$request->search}%"))
            ->latest()->paginate(15)->withQueryString();

        return view('purchase-orders.index', compact('orders'));
    }

    public function create()
    {
        return view('purchase-orders.form', [
            'suppliers' => Supplier::where('active', true)->pluck('name', 'id'),
            'warehouses' => Warehouse::where('active', true)->pluck('name', 'id'),
            'products' => Product::where('active', true)->with('units.unit')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.qty' => 'required|numeric|min:0.001',
            'items.*.price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $po = $this->purchaseService->createPurchaseOrder($data);

        return redirect()->route('purchase-orders.show', $po)->with('success', 'Purchase Order berhasil dibuat.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('items.product', 'items.unit', 'supplier', 'warehouse', 'goodsReceipts');

        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function print(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('items.product', 'items.unit', 'supplier', 'warehouse');

        return view('purchase-orders.print', compact('purchaseOrder'));
    }

    public function submit(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->update(['status' => 'submitted']);

        return back()->with('success', 'PO diajukan untuk approval.');
    }

    public function approve(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->update(['status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now()]);

        \App\Models\AuditLog::create([
            'user_id' => auth()->id(), 'action' => 'approve', 'module' => 'purchase_order',
            'record_type' => PurchaseOrder::class, 'record_id' => $purchaseOrder->id,
            'ip_address' => request()->ip(), 'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', 'PO berhasil disetujui.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'Hanya PO draft yang dapat dihapus.');
        }
        $purchaseOrder->items()->delete();
        $purchaseOrder->delete();

        return redirect()->route('purchase-orders.index')->with('success', 'PO berhasil dihapus.');
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        return redirect()->route('purchase-orders.show', $purchaseOrder);
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        return redirect()->route('purchase-orders.show', $purchaseOrder);
    }
}
