<?php

namespace App\Http\Controllers;

use App\Models\CashAccount;
use App\Models\DeliveryOrder;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseOrder;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockAdjustment;
use App\Models\StockOpname;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        [$periodFrom, $periodTo, $periodLabel] = $this->resolvePeriod($request);

        $todaySales = Sale::where('status', 'completed')->whereDate('sale_date', $today)->sum('total');
        $monthSales = Sale::where('status', 'completed')->where('sale_date', '>=', $monthStart)->sum('total');

        $periodSalesQuery = fn () => Sale::where('status', 'completed')->whereBetween('sale_date', [$periodFrom, $periodTo]);
        $periodSales = $periodSalesQuery()->sum('total');
        $periodTransactions = $periodSalesQuery()->count();

        $periodItems = SaleItem::whereHas('sale', fn ($q) => $q->where('status', 'completed')->whereBetween('sale_date', [$periodFrom, $periodTo]))->get();
        $periodProfit = $periodItems->sum(fn ($i) => ((float) $i->price - (float) $i->unit_cost) * (float) $i->qty - (float) $i->discount_amount);
        $periodMargin = $periodSales > 0 ? ($periodProfit / $periodSales) * 100 : 0;

        $grossProfit = Sale::where('status', 'completed')
            ->where('sale_date', '>=', $monthStart)
            ->with('items')
            ->get()
            ->flatMap->items
            ->sum(fn ($item) => ((float) $item->price - (float) $item->unit_cost) * (float) $item->qty - (float) $item->discount_amount);

        $receivable = Sale::where('is_credit', true)->where('status', '!=', 'void')->get()
            ->sum(fn ($s) => max(0, $s->total - $s->paid_amount));

        $payableSales = Purchase::get();
        $payable = $payableSales->sum(fn ($p) => max(0, $p->total - $p->paid_amount));
        $overduePayable = $payableSales->filter(fn ($p) => $p->isOverdue())->sum(fn ($p) => $p->outstanding());

        $activeProducts = Product::where('active', true)->get();
        $lowStock = $activeProducts->filter(fn ($p) => $p->isLowStock());
        $outOfStock = $activeProducts->filter(fn ($p) => $p->totalStock() <= 0);
        $inventoryValue = $activeProducts->sum(fn ($p) => $p->totalStock() * (float) $p->average_cost);

        $cashBalance = CashAccount::sum('current_balance');

        $recentSales = Sale::with('customer')->latest('sale_date')->take(6)->get();
        $lowStockProducts = $lowStock->sortBy(fn ($p) => $p->totalStock())->take(8);
        $overdueReceivables = Sale::where('is_credit', true)->where('status', '!=', 'void')
            ->whereNotNull('due_date')->where('due_date', '<', $today)
            ->get()->filter(fn ($s) => $s->outstanding() > 0)->sortByDesc(fn ($s) => $s->outstanding())->take(6);

        $salesByDay = Sale::where('status', 'completed')
            ->where('sale_date', '>=', Carbon::now()->subDays(13)->startOfDay())
            ->selectRaw('DATE(sale_date) as d, SUM(total) as total')
            ->groupBy('d')->orderBy('d')->pluck('total', 'd');

        $topProducts = SaleItem::whereHas('sale', fn ($q) => $q->where('status', 'completed')->whereBetween('sale_date', [$periodFrom, $periodTo]))
            ->selectRaw('product_id, SUM(qty) as total_qty, SUM(subtotal) as total_sales')
            ->groupBy('product_id')->with('product')->orderByDesc('total_sales')->take(5)->get();

        $topCustomers = Sale::where('status', 'completed')->whereBetween('sale_date', [$periodFrom, $periodTo])
            ->whereNotNull('customer_id')
            ->selectRaw('customer_id, SUM(total) as total_spent, COUNT(*) as trx_count')
            ->groupBy('customer_id')->with('customer')->orderByDesc('total_spent')->take(5)->get();

        $topSuppliers = Purchase::whereBetween('purchase_date', [$periodFrom, $periodTo])
            ->selectRaw('supplier_id, SUM(total) as total_purchased, COUNT(*) as trx_count')
            ->groupBy('supplier_id')->with('supplier')->orderByDesc('total_purchased')->take(5)->get();

        $categorySales = SaleItem::whereHas('sale', fn ($q) => $q->where('status', 'completed')->whereBetween('sale_date', [$periodFrom, $periodTo]))
            ->with('product.category')
            ->get()
            ->groupBy(fn ($i) => $i->product->category->name ?? 'Lainnya')
            ->map(fn ($group) => $group->sum('subtotal'))
            ->sortDesc()
            ->take(6);

        $paymentBreakdown = \App\Models\SalePayment::whereHas('sale', fn ($q) => $q->whereBetween('sale_date', [$periodFrom, $periodTo]))
            ->with('paymentMethod')
            ->get()
            ->groupBy(fn ($p) => $p->paymentMethod->name ?? 'Lainnya')
            ->map(fn ($group) => $group->sum('amount'))
            ->sortDesc();

        $pendingApprovals = [
            'purchase_orders' => PurchaseOrder::where('status', 'submitted')->count(),
            'stock_opnames' => StockOpname::where('status', 'review')->count(),
            'stock_adjustments' => StockAdjustment::where('status', 'pending')->count(),
        ];
        $pendingApprovalTotal = array_sum($pendingApprovals);

        $pendingDeliveries = DeliveryOrder::whereIn('status', ['pending', 'preparing'])->count();

        $insights = [];
        if ($lowStock->count() > 0) {
            $insights[] = "{$lowStock->count()} produk berada di bawah minimum stock.";
        }
        if ($outOfStock->count() > 0) {
            $insights[] = "{$outOfStock->count()} produk stoknya habis (0).";
        }
        if ($overdueReceivables->count() > 0) {
            $overdueTotal = number_format($overdueReceivables->sum(fn ($s) => $s->outstanding()), 0, ',', '.');
            $insights[] = "Piutang overdue mencapai Rp {$overdueTotal}.";
        }
        if ($overduePayable > 0) {
            $insights[] = 'Hutang jatuh tempo mencapai Rp '.number_format($overduePayable, 0, ',', '.').'.';
        }
        if ($topProducts->isNotEmpty()) {
            $insights[] = "Produk terlaris periode ini: {$topProducts->first()->product?->name}.";
        }
        if ($categorySales->isNotEmpty()) {
            $insights[] = 'Kategori dengan penjualan tertinggi: '.$categorySales->keys()->first().'.';
        }
        $lastMonthSales = Sale::where('status', 'completed')
            ->whereBetween('sale_date', [Carbon::now()->subMonthNoOverflow()->startOfMonth(), Carbon::now()->subMonthNoOverflow()->endOfMonth()])
            ->sum('total');
        if ($lastMonthSales > 0) {
            $growth = round((($monthSales - $lastMonthSales) / $lastMonthSales) * 100, 1);
            if ($growth != 0) {
                $dir = $growth > 0 ? 'meningkat' : 'menurun';
                $insights[] = "Penjualan bulan ini {$dir} ".abs($growth)."% dibanding bulan lalu.";
            }
        }

        return view('dashboard.index', compact(
            'todaySales', 'monthSales', 'grossProfit', 'receivable', 'payable', 'overduePayable',
            'lowStock', 'outOfStock', 'inventoryValue', 'cashBalance', 'recentSales', 'lowStockProducts',
            'overdueReceivables', 'salesByDay', 'insights', 'periodFrom', 'periodTo', 'periodLabel',
            'periodSales', 'periodTransactions', 'periodProfit', 'periodMargin',
            'topProducts', 'topCustomers', 'topSuppliers', 'categorySales', 'paymentBreakdown',
            'pendingApprovals', 'pendingApprovalTotal', 'pendingDeliveries'
        ));
    }

    private function resolvePeriod(Request $request): array
    {
        $period = $request->get('period', 'this_month');
        $now = Carbon::now();

        return match ($period) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay(), 'Hari Ini'],
            'yesterday' => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay(), 'Kemarin'],
            'this_week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek(), 'Minggu Ini'],
            'last_month' => [$now->copy()->subMonthNoOverflow()->startOfMonth(), $now->copy()->subMonthNoOverflow()->endOfMonth(), 'Bulan Lalu'],
            'custom' => [
                $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : $now->copy()->startOfMonth(),
                $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : $now->copy()->endOfDay(),
                'Custom Range',
            ],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfDay(), 'Bulan Ini'],
        };
    }
}
