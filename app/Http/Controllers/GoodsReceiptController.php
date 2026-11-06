<?php

namespace App\Http\Controllers;

use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\PurchaseService;
use Illuminate\Http\Request;

class GoodsReceiptController extends Controller
{
    public function __construct(private PurchaseService $purchaseService) {}

    public function index(Request $request)
    {
        $receipts = GoodsReceipt::with('supplier', 'warehouse')
            ->when($request->search, fn ($q) => $q->where('receipt_number', 'like', "%{$request->search}%"))
            ->latest()->paginate(15)->withQueryString();

        return view('goods-receipts.index', compact('receipts'));
    }

    public function create(Request $request)
    {
        $po = $request->purchase_order_id ? PurchaseOrder::with('items.product', 'items.unit')->find($request->purchase_order_id) : null;

        return view('goods-receipts.form', [
            'po' => $po,
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
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.qty' => 'required|numeric|min:0.001',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.damaged_qty' => 'nullable|numeric|min:0',
            'items.*.purchase_order_item_id' => 'nullable|exists:purchase_order_items,id',
            'notes' => 'nullable|string',
        ]);

        $receipt = $this->purchaseService->receiveGoods($data);

        return redirect()->route('goods-receipts.show', $receipt)->with('success', 'Barang berhasil diterima dan stok diperbarui.');
    }

    public function show(GoodsReceipt $goodsReceipt)
    {
        $goodsReceipt->load('items.product', 'items.unit', 'supplier', 'warehouse', 'purchase');

        return view('goods-receipts.show', compact('goodsReceipt'));
    }
}
