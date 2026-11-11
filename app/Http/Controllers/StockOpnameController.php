<?php

namespace App\Http\Controllers;

use App\Models\StockOpname;
use App\Models\Warehouse;
use App\Services\StockOpnameService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StockOpnameController extends Controller
{
    public function __construct(private StockOpnameService $service) {}

    public function index(Request $request)
    {
        $opnames = StockOpname::with('warehouse', 'counter')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()->paginate(15)->withQueryString();

        return view('stock-opnames.index', compact('opnames'));
    }

    public function create()
    {
        return view('stock-opnames.create', ['warehouses' => Warehouse::where('active', true)->pluck('name', 'id')]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $opname = $this->service->create($data['warehouse_id'], $data['date'], $data['notes'] ?? null);

        return redirect()->route('stock-opnames.show', $opname)->with('success', 'Stock Opname dibuat. Silakan lakukan penghitungan fisik.');
    }

    public function show(StockOpname $stockOpname)
    {
        $stockOpname->load('items.product', 'warehouse', 'counter', 'reviewer', 'approver');

        $totalSku = $stockOpname->items->count();
        $counted = $stockOpname->items->whereNotNull('physical_qty')->count();
        $increase = $stockOpname->items->filter(fn ($i) => ($i->difference_qty ?? 0) > 0)->count();
        $decrease = $stockOpname->items->filter(fn ($i) => ($i->difference_qty ?? 0) < 0)->count();
        $differenceValue = $stockOpname->items->sum('difference_value');

        return view('stock-opnames.show', compact('stockOpname', 'totalSku', 'counted', 'increase', 'decrease', 'differenceValue'));
    }

    public function print(StockOpname $stockOpname)
    {
        $stockOpname->load('items.product', 'warehouse', 'counter', 'reviewer', 'approver');

        return view('stock-opnames.print', compact('stockOpname'));
    }

    public function saveCount(Request $request, StockOpname $stockOpname)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:stock_opname_items,id',
            'items.*.physical_qty' => 'nullable|numeric|min:0',
            'items.*.reason' => 'nullable|in:damaged,missing,found,wrong_entry,wrong_unit,receiving_error,other',
            'items.*.notes' => 'nullable|string',
        ]);

        $this->service->saveCount($stockOpname, $data['items']);

        return back()->with('success', 'Hasil penghitungan berhasil disimpan.');
    }

    public function submitReview(StockOpname $stockOpname)
    {
        $this->service->submitReview($stockOpname);

        return back()->with('success', 'Opname diajukan untuk review.');
    }

    public function approve(StockOpname $stockOpname)
    {
        $this->service->approve($stockOpname);

        return back()->with('success', 'Opname disetujui. Siap untuk diposting.');
    }

    public function post(StockOpname $stockOpname)
    {
        try {
            $this->service->post($stockOpname);
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }

        return back()->with('success', 'Opname berhasil diposting. Stok telah disesuaikan.');
    }
}
