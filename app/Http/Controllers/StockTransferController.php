<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Services\StockTransferService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StockTransferController extends Controller
{
    public function __construct(private StockTransferService $service) {}

    public function index(Request $request)
    {
        $transfers = StockTransfer::with('sourceWarehouse', 'destinationWarehouse')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()->paginate(15)->withQueryString();

        return view('stock-transfers.index', compact('transfers'));
    }

    public function create()
    {
        return view('stock-transfers.form', [
            'warehouses' => Warehouse::where('active', true)->pluck('name', 'id'),
            'products' => Product::where('active', true)->with('units.unit')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'source_warehouse_id' => 'required|exists:warehouses,id',
            'destination_warehouse_id' => 'required|exists:warehouses,id|different:source_warehouse_id',
            'transfer_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.qty' => 'required|numeric|min:0.001',
            'notes' => 'nullable|string',
        ]);

        try {
            $transfer = $this->service->create($data);
        } catch (ValidationException $e) {
            return back()->withInput()->with('error', collect($e->errors())->flatten()->first());
        }

        return redirect()->route('stock-transfers.show', $transfer)->with('success', 'Stock Transfer berhasil dibuat.');
    }

    public function show(StockTransfer $stockTransfer)
    {
        $stockTransfer->load('items.product', 'items.unit', 'sourceWarehouse', 'destinationWarehouse', 'requester', 'approver');

        return view('stock-transfers.show', compact('stockTransfer'));
    }

    public function approve(StockTransfer $stockTransfer)
    {
        $this->service->approve($stockTransfer);

        return back()->with('success', 'Stock Transfer disetujui.');
    }

    public function ship(StockTransfer $stockTransfer)
    {
        try {
            $this->service->ship($stockTransfer);
        } catch (\App\Exceptions\InsufficientStockException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Barang dikirim, stok gudang asal telah dikurangi.');
    }

    public function receive(StockTransfer $stockTransfer)
    {
        $this->service->receive($stockTransfer);

        return back()->with('success', 'Barang diterima, stok gudang tujuan telah ditambah.');
    }
}
