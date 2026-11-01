<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $sales = Sale::with('customer')
            ->when($request->search, fn ($q) => $q->where('invoice_number', 'like', "%{$request->search}%"))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->date_from, fn ($q) => $q->whereDate('sale_date', '>=', $request->date_from))
            ->when($request->date_to, fn ($q) => $q->whereDate('sale_date', '<=', $request->date_to))
            ->latest('sale_date')->paginate(15)->withQueryString();

        return view('sales.index', compact('sales'));
    }

    public function show(Sale $sale)
    {
        $sale->load(['items.product', 'items.unit', 'payments.paymentMethod', 'customer', 'warehouse', 'cashier', 'returns']);

        return view('sales.show', compact('sale'));
    }

    public function print(Sale $sale)
    {
        $sale->load(['items.product', 'items.unit', 'payments.paymentMethod', 'customer', 'warehouse']);

        return view('sales.print', compact('sale'));
    }

    public function receipt(Sale $sale)
    {
        $sale->load(['items.product', 'items.unit', 'payments.paymentMethod', 'customer']);

        return view('sales.receipt', compact('sale'));
    }
}
