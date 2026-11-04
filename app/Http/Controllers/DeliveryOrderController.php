<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\Driver;
use App\Models\Sale;
use App\Models\Vehicle;
use App\Services\NumberingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryOrderController extends Controller
{
    public function __construct(private NumberingService $numbering) {}

    public function index(Request $request)
    {
        $deliveries = DeliveryOrder::with('customer', 'driver', 'vehicle')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()->paginate(15)->withQueryString();

        return view('delivery-orders.index', compact('deliveries'));
    }

    public function create(Request $request)
    {
        $sale = null;
        if ($request->invoice) {
            $sale = Sale::with('items.product', 'items.unit', 'customer')->where('invoice_number', $request->invoice)->first();
            if (! $sale) {
                return redirect()->route('delivery-orders.create')->with('error', 'Invoice tidak ditemukan.');
            }
        }

        return view('delivery-orders.create', [
            'sale' => $sale,
            'drivers' => Driver::where('active', true)->pluck('name', 'id'),
            'vehicles' => Vehicle::where('active', true)->pluck('plate_number', 'id'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'delivery_address' => 'nullable|string',
            'driver_id' => 'nullable|exists:drivers,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'delivery_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.sale_item_id' => 'required|exists:sale_items,id',
            'items.*.qty' => 'required|numeric|min:0.001',
        ]);

        $sale = Sale::with('customer')->findOrFail($data['sale_id']);

        $delivery = DB::transaction(function () use ($data, $sale) {
            $delivery = DeliveryOrder::create([
                'do_number' => $this->numbering->next('DO'),
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'delivery_address' => $data['delivery_address'] ?? $sale->customer?->address,
                'driver_id' => $data['driver_id'] ?? null,
                'vehicle_id' => $data['vehicle_id'] ?? null,
                'delivery_date' => $data['delivery_date'],
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $saleItem = \App\Models\SaleItem::find($item['sale_item_id']);
                $delivery->items()->create([
                    'sale_item_id' => $saleItem->id,
                    'product_id' => $saleItem->product_id,
                    'unit_id' => $saleItem->unit_id,
                    'qty' => $item['qty'],
                ]);
            }

            return $delivery;
        });

        return redirect()->route('delivery-orders.show', $delivery)->with('success', 'Delivery Order berhasil dibuat.');
    }

    public function show(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load('items.product', 'items.unit', 'customer', 'driver', 'vehicle', 'sale');

        return view('delivery-orders.show', compact('deliveryOrder'));
    }

    public function print(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load('items.product', 'items.unit', 'customer', 'driver', 'vehicle');

        return view('delivery-orders.print', compact('deliveryOrder'));
    }

    public function edit(DeliveryOrder $deliveryOrder)
    {
        return view('delivery-orders.edit', [
            'deliveryOrder' => $deliveryOrder,
            'drivers' => Driver::where('active', true)->pluck('name', 'id'),
            'vehicles' => Vehicle::where('active', true)->pluck('plate_number', 'id'),
        ]);
    }

    public function update(Request $request, DeliveryOrder $deliveryOrder)
    {
        $data = $request->validate([
            'driver_id' => 'nullable|exists:drivers,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'delivery_date' => 'required|date',
            'status' => 'required|in:pending,preparing,shipped,delivered,cancelled',
            'proof_of_delivery' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $deliveryOrder->update($data);

        return redirect()->route('delivery-orders.show', $deliveryOrder)->with('success', 'Delivery Order berhasil diperbarui.');
    }

    public function destroy(DeliveryOrder $deliveryOrder)
    {
        if ($deliveryOrder->status === 'delivered') {
            return back()->with('error', 'Delivery yang sudah selesai tidak dapat dihapus.');
        }
        $deliveryOrder->items()->delete();
        $deliveryOrder->delete();

        return redirect()->route('delivery-orders.index')->with('success', 'Delivery Order berhasil dihapus.');
    }
}
