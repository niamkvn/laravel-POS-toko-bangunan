<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use App\Models\Customer;
use App\Models\CustomerProject;
use App\Models\DeliveryOrder;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockOpname;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    private function dateRange(Request $request): array
    {
        return [
            $request->date_from ?: Carbon::now()->startOfMonth()->format('Y-m-d'),
            $request->date_to ?: Carbon::now()->format('Y-m-d'),
        ];
    }

    public function sales(Request $request)
    {
        [$from, $to] = $this->dateRange($request);

        $sales = Sale::with('customer')
            ->where('status', 'completed')
            ->whereBetween('sale_date', [$from, "{$to} 23:59:59"])
            ->when($request->customer_id, fn ($q) => $q->where('customer_id', $request->customer_id))
            ->orderBy('sale_date')->get();

        $summary = [
            'total_sales' => $sales->sum('total'),
            'total_transactions' => $sales->count(),
            'average' => $sales->count() ? $sales->sum('total') / $sales->count() : 0,
        ];
        $customers = Customer::pluck('name', 'id');

        return view('reports.sales', compact('sales', 'summary', 'from', 'to', 'customers'));
    }

    public function purchases(Request $request)
    {
        [$from, $to] = $this->dateRange($request);

        $purchases = Purchase::with('supplier')
            ->whereBetween('purchase_date', [$from, $to])
            ->when($request->supplier_id, fn ($q) => $q->where('supplier_id', $request->supplier_id))
            ->orderBy('purchase_date')->get();

        $summary = ['total_purchases' => $purchases->sum('total'), 'total_transactions' => $purchases->count()];
        $suppliers = Supplier::pluck('name', 'id');

        return view('reports.purchases', compact('purchases', 'summary', 'from', 'to', 'suppliers'));
    }

    public function products(Request $request)
    {
        [$from, $to] = $this->dateRange($request);

        $items = SaleItem::whereHas('sale', fn ($q) => $q->where('status', 'completed')->whereBetween('sale_date', [$from, "{$to} 23:59:59"]))
            ->selectRaw('product_id, SUM(qty) as total_qty, SUM(subtotal) as total_sales, SUM((price - unit_cost) * qty - discount_amount) as gross_profit')
            ->groupBy('product_id')
            ->with('product')
            ->orderByDesc('total_sales')
            ->get();

        return view('reports.products', compact('items', 'from', 'to'));
    }

    public function stock()
    {
        $products = Product::with(['stocks.warehouse', 'category'])->where('active', true)->orderBy('name')->get();

        $inventoryValue = $products->sum(fn ($p) => $p->totalStock() * (float) $p->average_cost);

        return view('reports.stock', compact('products', 'inventoryValue'));
    }

    public function stockOpname(Request $request)
    {
        $opnames = StockOpname::with('warehouse')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()->paginate(20)->withQueryString();

        return view('reports.stock-opname', compact('opnames'));
    }

    public function receivable()
    {
        return redirect()->route('customers.receivables');
    }

    public function payable()
    {
        return redirect()->route('suppliers.payables');
    }

    public function cashFlow(Request $request)
    {
        [$from, $to] = $this->dateRange($request);

        $transactions = CashTransaction::with('cashAccount')
            ->whereBetween('transaction_date', [$from, $to])
            ->orderBy('transaction_date')->get();

        $totalIn = $transactions->whereIn('type', ['in', 'transfer_in'])->sum('amount');
        $totalOut = $transactions->whereIn('type', ['out', 'transfer_out'])->sum('amount');

        return view('reports.cash-flow', compact('transactions', 'totalIn', 'totalOut', 'from', 'to'));
    }

    public function profitLoss(Request $request)
    {
        [$from, $to] = $this->dateRange($request);

        $sales = Sale::where('status', 'completed')->whereBetween('sale_date', [$from, "{$to} 23:59:59"])->with('items')->get();
        $revenue = $sales->sum('total');
        $cogs = $sales->flatMap->items->sum(fn ($i) => $i->unit_cost * $i->qty);
        $grossProfit = $revenue - $cogs;
        $expenses = \App\Models\Expense::whereBetween('expense_date', [$from, $to])->sum('amount');
        $netProfit = $grossProfit - $expenses;

        return view('reports.profit-loss', compact('revenue', 'cogs', 'grossProfit', 'expenses', 'netProfit', 'from', 'to'));
    }

    public function profitability(Request $request)
    {
        [$from, $to] = $this->dateRange($request);

        $items = SaleItem::whereHas('sale', fn ($q) => $q->where('status', 'completed')->whereBetween('sale_date', [$from, "{$to} 23:59:59"]))
            ->selectRaw('product_id, SUM(qty) as qty, SUM(subtotal) as revenue, SUM(unit_cost * qty) as cost, SUM((price - unit_cost) * qty - discount_amount) as profit')
            ->groupBy('product_id')->with('product.category')->get()
            ->map(function ($i) {
                $i->margin = $i->revenue > 0 ? ($i->profit / $i->revenue) * 100 : 0;

                return $i;
            })->sortByDesc('margin');

        return view('reports.profitability', compact('items', 'from', 'to'));
    }

    public function customers(Request $request)
    {
        $customers = Customer::withCount('sales')
            ->withSum(['sales as total_purchase' => fn ($q) => $q->where('status', 'completed')], 'total')
            ->orderByDesc('total_purchase')->paginate(20);

        return view('reports.customers', compact('customers'));
    }

    public function suppliers(Request $request)
    {
        $suppliers = Supplier::withCount('purchases')
            ->withSum('purchases as total_purchase', 'total')
            ->orderByDesc('total_purchase')->paginate(20);

        return view('reports.suppliers', compact('suppliers'));
    }

    public function projects()
    {
        $projects = CustomerProject::with('customer')
            ->withSum(['sales as total_sales' => fn ($q) => $q->where('status', 'completed')], 'total')
            ->paginate(20);

        return view('reports.projects', compact('projects'));
    }

    public function deliveries(Request $request)
    {
        $deliveries = DeliveryOrder::with('customer', 'driver', 'vehicle')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()->paginate(20)->withQueryString();

        return view('reports.deliveries', compact('deliveries'));
    }

    public function exportSales(Request $request)
    {
        [$from, $to] = $this->dateRange($request);
        $sales = Sale::where('status', 'completed')->whereBetween('sale_date', [$from, "{$to} 23:59:59"])->with('customer')->get();

        return response()->streamDownload(function () use ($sales) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Invoice', 'Tanggal', 'Customer', 'Subtotal', 'Diskon', 'Total', 'Status']);
            foreach ($sales as $s) {
                fputcsv($out, [$s->invoice_number, $s->sale_date->format('Y-m-d H:i'), $s->customer->name ?? 'Umum', $s->subtotal, $s->discount_amount, $s->total, $s->status]);
            }
            fclose($out);
        }, 'laporan-penjualan.csv');
    }

    public function exportPurchases(Request $request)
    {
        [$from, $to] = $this->dateRange($request);
        $purchases = Purchase::whereBetween('purchase_date', [$from, $to])->with('supplier')->get();

        return response()->streamDownload(function () use ($purchases) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['No. Pembelian', 'Tanggal', 'Supplier', 'Total', 'Dibayar', 'Status']);
            foreach ($purchases as $p) {
                fputcsv($out, [$p->purchase_number, $p->purchase_date->format('Y-m-d'), $p->supplier->name ?? '-', $p->total, $p->paid_amount, $p->status]);
            }
            fclose($out);
        }, 'laporan-pembelian.csv');
    }
}
