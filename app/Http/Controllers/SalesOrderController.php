<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerProject;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\Warehouse;
use App\Services\NumberingService;
use App\Services\SalesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalesOrderController extends Controller
{
    public function __construct(private NumberingService $numbering, private SalesService $salesService) {}

    public function index(Request $request)
    {
        $orders = SalesOrder::with('customer')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()->paginate(15)->withQueryString();

        return view('sales-orders.index', compact('orders'));
    }

    public function create()
    {
        return view('sales-orders.form', [
            'customers' => Customer::where('active', true)->pluck('name', 'id'),
            'projects' => CustomerProject::pluck('name', 'id'),
            'products' => Product::where('active', true)->with('units.unit')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'customer_project_id' => 'nullable|exists:customer_projects,id',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.qty' => 'required|numeric|min:0.001',
            'items.*.price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($data) {
            $subtotal = collect($data['items'])->sum(fn ($i) => $i['qty'] * $i['price']);

            $so = SalesOrder::create([
                'number' => $this->numbering->next('SO'),
                'customer_id' => $data['customer_id'],
                'customer_project_id' => $data['customer_project_id'] ?? null,
                'date' => $data['date'],
                'status' => 'confirmed',
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($data['items'] as $item) {
                $so->items()->create([
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['qty'] * $item['price'],
                ]);
            }
        });

        return redirect()->route('sales-orders.index')->with('success', 'Sales Order berhasil dibuat.');
    }

    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load('items.product', 'items.unit', 'customer', 'project', 'sales');

        return view('sales-orders.show', compact('salesOrder'));
    }

    public function fulfillForm(SalesOrder $salesOrder)
    {
        $salesOrder->load('items.product.units.unit');
        $warehouses = Warehouse::where('active', true)->pluck('name', 'id');

        return view('sales-orders.fulfill', compact('salesOrder', 'warehouses'));
    }

    public function fulfill(Request $request, SalesOrder $salesOrder)
    {
        $data = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'items' => 'required|array|min:1',
            'items.*.sales_order_item_id' => 'required|exists:sales_order_items,id',
            'items.*.qty' => 'required|numeric|min:0',
            'payments' => 'required|array|min:1',
            'payments.*.payment_method_id' => 'required|exists:payment_methods,id',
            'payments.*.amount' => 'required|numeric|min:0',
        ]);

        $items = collect($data['items'])->filter(fn ($i) => $i['qty'] > 0);
        if ($items->isEmpty()) {
            return back()->with('error', 'Pilih minimal 1 item untuk difulfill.');
        }

        $saleItems = [];
        foreach ($items as $i) {
            $soItem = \App\Models\SalesOrderItem::find($i['sales_order_item_id']);
            if ($i['qty'] > $soItem->remainingQty() + 0.001) {
                return back()->with('error', "Qty fulfill melebihi sisa order untuk {$soItem->product->name}.");
            }
            $saleItems[] = [
                'product_id' => $soItem->product_id,
                'unit_id' => $soItem->unit_id,
                'qty' => $i['qty'],
                'price' => $soItem->price,
            ];
        }

        try {
            $sale = $this->salesService->createSale([
                'warehouse_id' => $data['warehouse_id'],
                'customer_id' => $salesOrder->customer_id,
                'customer_project_id' => $salesOrder->customer_project_id,
                'sales_order_id' => $salesOrder->id,
                'status' => 'completed',
                'items' => $saleItems,
                'payments' => $data['payments'],
            ]);
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }

        foreach ($items as $i) {
            $soItem = \App\Models\SalesOrderItem::find($i['sales_order_item_id']);
            $soItem->increment('fulfilled_qty', $i['qty']);
        }

        $salesOrder->refresh();
        $allFulfilled = $salesOrder->items->every(fn ($i) => $i->fulfilled_qty >= $i->qty - 0.001);
        $anyFulfilled = $salesOrder->items->sum('fulfilled_qty') > 0;
        $salesOrder->update(['status' => $allFulfilled ? 'fulfilled' : ($anyFulfilled ? 'partially_fulfilled' : 'confirmed')]);

        return redirect()->route('sales.show', $sale)->with('success', 'Sales Order berhasil difulfill menjadi penjualan.');
    }
}
