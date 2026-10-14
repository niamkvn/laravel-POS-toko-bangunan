<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\ProductUnit;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\PriceLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'brand', 'baseUnit'])
            ->when($request->search, fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('sku', 'like', "%{$request->search}%")
                    ->orWhere('barcode', 'like', "%{$request->search}%");
            }))
            ->when($request->category_id, fn ($q) => $q->where('category_id', $request->category_id))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $categories = Category::orderBy('name')->pluck('name', 'id');

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        return view('products.form', [
            'product' => new Product,
            'categories' => Category::where('active', true)->orderBy('name')->pluck('name', 'id'),
            'brands' => Brand::where('active', true)->orderBy('name')->pluck('name', 'id'),
            'units' => Unit::orderBy('name')->pluck('name', 'id'),
            'taxes' => Tax::where('active', true)->pluck('name', 'id'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data, $request) {
            $product = Product::create(array_merge($data, [
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]));

            ProductUnit::create([
                'product_id' => $product->id,
                'unit_id' => $product->unit_id,
                'conversion_factor' => 1,
                'is_base' => true,
                'is_purchase_unit' => true,
                'is_sale_unit' => true,
            ]);

            foreach ($request->input('extra_units', []) as $eu) {
                if (empty($eu['unit_id']) || empty($eu['conversion_factor'])) {
                    continue;
                }
                ProductUnit::create([
                    'product_id' => $product->id,
                    'unit_id' => $eu['unit_id'],
                    'conversion_factor' => $eu['conversion_factor'],
                    'is_purchase_unit' => ! empty($eu['is_purchase_unit']),
                    'is_sale_unit' => ! empty($eu['is_sale_unit']),
                ]);
            }

            \App\Models\AuditLog::create([
                'user_id' => auth()->id(), 'action' => 'create', 'module' => 'product',
                'record_type' => Product::class, 'record_id' => $product->id,
                'new_values' => $product->toArray(),
                'ip_address' => request()->ip(), 'user_agent' => request()->userAgent(),
            ]);
        });

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'baseUnit', 'units.unit', 'prices.priceLevel', 'stocks.warehouse']);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load('units.unit');

        return view('products.form', [
            'product' => $product,
            'categories' => Category::where('active', true)->orderBy('name')->pluck('name', 'id'),
            'brands' => Brand::where('active', true)->orderBy('name')->pluck('name', 'id'),
            'units' => Unit::orderBy('name')->pluck('name', 'id'),
            'taxes' => Tax::where('active', true)->pluck('name', 'id'),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request, $product->id);
        $oldValues = $product->toArray();

        DB::transaction(function () use ($data, $product, $request, $oldValues) {
            $product->update(array_merge($data, ['updated_by' => auth()->id()]));

            $product->units()->where('is_base', false)->delete();
            foreach ($request->input('extra_units', []) as $eu) {
                if (empty($eu['unit_id']) || empty($eu['conversion_factor'])) {
                    continue;
                }
                ProductUnit::create([
                    'product_id' => $product->id,
                    'unit_id' => $eu['unit_id'],
                    'conversion_factor' => $eu['conversion_factor'],
                    'is_purchase_unit' => ! empty($eu['is_purchase_unit']),
                    'is_sale_unit' => ! empty($eu['is_sale_unit']),
                ]);
            }

            \App\Models\AuditLog::create([
                'user_id' => auth()->id(), 'action' => 'update', 'module' => 'product',
                'record_type' => Product::class, 'record_id' => $product->id,
                'old_values' => $oldValues, 'new_values' => $product->fresh()->toArray(),
                'ip_address' => request()->ip(), 'user_agent' => request()->userAgent(),
            ]);
        });

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->stockMovements()->exists()) {
            return back()->with('error', 'Produk tidak dapat dihapus karena sudah memiliki riwayat transaksi.');
        }
        $product->units()->delete();
        $product->prices()->delete();
        $product->stocks()->delete();
        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }

    public function barcode(Request $request)
    {
        $products = Product::when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->where('active', true)->orderBy('name')->paginate(24)->withQueryString();

        return view('products.barcode', compact('products'));
    }

    public function prices(Product $product)
    {
        $product->load('prices.priceLevel');
        $priceLevels = PriceLevel::orderBy('name')->pluck('name', 'id');

        return view('products.prices', compact('product', 'priceLevels'));
    }

    public function storePrice(Request $request, Product $product)
    {
        $data = $request->validate([
            'price_level_id' => 'nullable|exists:price_levels,id',
            'min_qty' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:0',
        ]);
        $data['product_id'] = $product->id;
        $data['active'] = true;
        ProductPrice::create($data);

        return back()->with('success', 'Harga berhasil ditambahkan.');
    }

    public function destroyPrice(ProductPrice $productPrice)
    {
        $productId = $productPrice->product_id;
        $productPrice->delete();

        return redirect()->route('products.prices', $productId)->with('success', 'Harga berhasil dihapus.');
    }

    private function validated(Request $request, $id = null): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'sku' => 'required|string|max:50|unique:products,sku'.($id ? ",{$id}" : ''),
            'barcode' => 'nullable|string|max:50|unique:products,barcode'.($id ? ",{$id}" : ''),
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'required|exists:units,id',
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'default_sell_price' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
            'maximum_stock' => 'nullable|numeric|min:0',
            'tax_id' => 'nullable|exists:taxes,id',
            'allow_negative_stock' => 'boolean',
            'active' => 'boolean',
        ]);

        $data['product_code'] = $id ? Product::find($id)->product_code : 'PRD-'.strtoupper(Str::random(6));
        $data['allow_negative_stock'] = $request->boolean('allow_negative_stock');
        $data['active'] = $request->boolean('active', true);
        if (! $id) {
            $data['average_cost'] = $data['purchase_price'];
        }

        return $data;
    }
}
