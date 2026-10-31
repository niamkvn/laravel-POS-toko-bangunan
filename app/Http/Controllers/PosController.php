<?php

namespace App\Http\Controllers;

use App\Models\CashierShift;
use App\Models\Category;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\PriceLevel;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Warehouse;
use App\Services\PricingService;
use App\Services\SalesService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PosController extends Controller
{
    public function __construct(private SalesService $salesService, private PricingService $pricing) {}

    public function index()
    {
        $shift = CashierShift::where('user_id', auth()->id())->where('status', 'open')->first();
        if (! $shift) {
            return redirect()->route('cashier-shifts.open-form')->with('error', 'Anda harus membuka shift kasir terlebih dahulu sebelum menggunakan POS.');
        }

        $categories = Category::where('active', true)->orderBy('name')->get();
        $warehouses = Warehouse::where('active', true)->get();
        $priceLevels = PriceLevel::all();
        $paymentMethods = PaymentMethod::where('active', true)->get();
        $defaultWarehouse = $warehouses->firstWhere('is_default', true) ?? $warehouses->first();
        $heldSales = Sale::with(['items.product', 'items.unit', 'customer'])
            ->where('status', 'held')->where('user_id', auth()->id())->latest()->get();

        return view('pos.index', compact('categories', 'warehouses', 'priceLevels', 'paymentMethods', 'defaultWarehouse', 'shift', 'heldSales'));
    }

    public function products(Request $request)
    {
        $warehouseId = $request->warehouse_id;
        $customer = $request->customer_id ? Customer::find($request->customer_id) : null;
        $priceLevel = $request->price_level_id ? PriceLevel::find($request->price_level_id) : null;

        $products = Product::with(['units.unit', 'baseUnit', 'stocks'])
            ->where('active', true)
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                        ->orWhere('sku', 'like', "%{$request->search}%")
                        ->orWhere('barcode', 'like', "%{$request->search}%");
                });
            })
            ->when($request->category_id, fn ($q) => $q->where('category_id', $request->category_id))
            ->orderBy('name')
            ->limit(60)
            ->get();

        $result = $products->map(function ($product) use ($warehouseId, $customer, $priceLevel) {
            $stock = $warehouseId ? (float) $product->stocks->where('warehouse_id', $warehouseId)->sum('quantity') : $product->totalStock();
            $units = $product->units->map(function ($u) use ($product, $customer, $priceLevel) {
                $priced = $this->pricing->priceFor($product, $customer, 1, $priceLevel);

                return [
                    'unit_id' => $u->unit_id,
                    'name' => $u->unit->name,
                    'code' => $u->unit->code,
                    'conversion_factor' => (float) $u->conversion_factor,
                    'price' => $priced['price'] * (float) $u->conversion_factor,
                    'is_sale_unit' => (bool) $u->is_sale_unit,
                ];
            })->filter(fn ($u) => $u['is_sale_unit'])->values();

            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'stock' => $stock,
                'allow_negative_stock' => (bool) $product->allow_negative_stock,
                'units' => $units,
            ];
        })->filter(fn ($p) => count($p['units']) > 0)->values();

        return response()->json($result);
    }

    public function customers(Request $request)
    {
        $customers = Customer::where('active', true)
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->orderBy('name')->limit(20)->get(['id', 'name', 'customer_type', 'price_level_id']);

        return response()->json($customers);
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'customer_id' => 'nullable|exists:customers,id',
            'price_level_id' => 'nullable|exists:price_levels,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.qty' => 'required|numeric|min:0.001',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'payments' => 'required|array|min:1',
            'payments.*.payment_method_id' => 'required|exists:payment_methods,id',
            'payments.*.amount' => 'required|numeric|min:0',
            'resume_sale_id' => 'nullable|exists:sales,id',
        ]);

        $shift = CashierShift::where('user_id', auth()->id())->where('status', 'open')->first();
        if (! $shift) {
            return back()->with('error', 'Shift kasir tidak aktif.');
        }

        $discountAmount = (float) ($data['discount_amount'] ?? 0);
        $subtotalPreview = collect($data['items'])->sum(fn ($i) => $i['price'] * $i['qty'] - ($i['discount_amount'] ?? 0));
        if ($subtotalPreview > 0) {
            $discountPercent = ($discountAmount / $subtotalPreview) * 100;
            $managerThreshold = (float) (\App\Models\Setting::get('discount_manager_threshold', 5));
            $ownerThreshold = (float) (\App\Models\Setting::get('discount_owner_threshold', 15));
            if ($discountPercent > $ownerThreshold && ! auth()->user()->hasRole(['Owner', 'Super Admin'])) {
                return back()->withInput()->with('error', 'Diskon melebihi '.$ownerThreshold.'% membutuhkan approval Owner.');
            }
            if ($discountPercent > $managerThreshold && ! auth()->user()->hasRole(['Manager', 'Owner', 'Super Admin'])) {
                return back()->withInput()->with('error', 'Diskon melebihi '.$managerThreshold.'% membutuhkan approval Manager.');
            }
        }

        try {
            $sale = $this->salesService->createSale([
                ...$data,
                'status' => 'completed',
                'cashier_shift_id' => $shift->id,
            ]);
        } catch (ValidationException $e) {
            return back()->withInput()->with('error', collect($e->errors())->flatten()->first());
        }

        if (! empty($data['resume_sale_id'])) {
            Sale::where('id', $data['resume_sale_id'])->where('status', 'held')->delete();
        }

        return redirect()->route('sales.receipt', $sale)->with('success', 'Transaksi berhasil disimpan.');
    }

    public function hold(Request $request)
    {
        $data = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'customer_id' => 'nullable|exists:customers,id',
            'price_level_id' => 'nullable|exists:price_levels,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.qty' => 'required|numeric|min:0.001',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $this->salesService->createSale([...$data, 'status' => 'held', 'payments' => []]);

        return redirect()->route('pos.index')->with('success', 'Transaksi berhasil ditahan (hold).');
    }

    public function held()
    {
        $sales = Sale::with('items.product', 'items.unit', 'customer')
            ->where('status', 'held')->where('user_id', auth()->id())->latest()->get();

        return response()->json($sales);
    }

    public function void(Sale $sale, Request $request)
    {
        $request->validate(['reason' => 'required|string|max:255']);

        try {
            $this->salesService->voidSale($sale, $request->reason);
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }

        return back()->with('success', 'Transaksi berhasil di-void.');
    }
}
