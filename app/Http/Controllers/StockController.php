<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['stocks.warehouse', 'category', 'baseUnit'])
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->when($request->warehouse_id, fn ($q) => $q->whereHas('stocks', fn ($s) => $s->where('warehouse_id', $request->warehouse_id)->where('quantity', '>', 0)))
            ->when($request->boolean('low_stock'), fn ($q) => $q->whereRaw(
                '(select coalesce(sum(quantity),0) from stocks where stocks.product_id = products.id) <= products.minimum_stock'
            ))
            ->orderBy('name')
            ->paginate(20)->withQueryString();

        $warehouses = Warehouse::pluck('name', 'id');

        return view('stocks.index', compact('products', 'warehouses'));
    }

    public function card(Request $request)
    {
        $products = Product::orderBy('name')->pluck('name', 'id');
        $warehouses = Warehouse::pluck('name', 'id');

        $movements = collect();
        if ($request->product_id) {
            $movements = StockMovement::where('product_id', $request->product_id)
                ->when($request->warehouse_id, fn ($q) => $q->where('warehouse_id', $request->warehouse_id))
                ->when($request->date_from, fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
                ->when($request->date_to, fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
                ->with('warehouse')
                ->latest()
                ->paginate(30)->withQueryString();
        }

        return view('stocks.card', compact('products', 'warehouses', 'movements'));
    }
}
