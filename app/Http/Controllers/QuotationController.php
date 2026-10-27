<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerProject;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\SalesOrder;
use App\Services\NumberingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function __construct(private NumberingService $numbering) {}

    public function index(Request $request)
    {
        $quotations = Quotation::with('customer')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()->paginate(15)->withQueryString();

        return view('quotations.index', compact('quotations'));
    }

    public function create()
    {
        return view('quotations.form', [
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
            'valid_until' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.qty' => 'required|numeric|min:0.001',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($data) {
            $subtotal = collect($data['items'])->sum(fn ($i) => $i['qty'] * $i['price'] - ($i['discount_amount'] ?? 0));

            $quotation = Quotation::create([
                'number' => $this->numbering->next('QUO'),
                'customer_id' => $data['customer_id'],
                'customer_project_id' => $data['customer_project_id'] ?? null,
                'date' => $data['date'],
                'valid_until' => $data['valid_until'] ?? null,
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'notes' => $data['notes'] ?? null,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            foreach ($data['items'] as $item) {
                $quotation->items()->create([
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'subtotal' => $item['qty'] * $item['price'] - ($item['discount_amount'] ?? 0),
                ]);
            }

            return $quotation;
        });

        return redirect()->route('quotations.index')->with('success', 'Quotation berhasil dibuat.');
    }

    public function show(Quotation $quotation)
    {
        $quotation->load('items.product', 'items.unit', 'customer', 'project', 'salesOrder');

        return view('quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        return redirect()->route('quotations.show', $quotation);
    }

    public function update(Request $request, Quotation $quotation)
    {
        return redirect()->route('quotations.show', $quotation);
    }

    public function destroy(Quotation $quotation)
    {
        if ($quotation->status === 'converted') {
            return back()->with('error', 'Quotation yang sudah dikonversi tidak dapat dihapus.');
        }
        $quotation->items()->delete();
        $quotation->delete();

        return redirect()->route('quotations.index')->with('success', 'Quotation berhasil dihapus.');
    }

    public function send(Quotation $quotation)
    {
        $quotation->update(['status' => 'sent']);

        return back()->with('success', 'Quotation ditandai terkirim.');
    }

    public function print(Quotation $quotation)
    {
        $quotation->load('items.product', 'items.unit', 'customer');

        return view('quotations.print', compact('quotation'));
    }

    public function approve(Quotation $quotation)
    {
        $quotation->update(['status' => 'approved']);

        return back()->with('success', 'Quotation disetujui customer.');
    }

    public function reject(Quotation $quotation)
    {
        $quotation->update(['status' => 'rejected']);

        return back()->with('success', 'Quotation ditolak.');
    }

    public function convert(Quotation $quotation)
    {
        if ($quotation->status === 'converted') {
            return back()->with('error', 'Quotation sudah dikonversi.');
        }

        $so = DB::transaction(function () use ($quotation) {
            $salesOrder = SalesOrder::create([
                'number' => $this->numbering->next('SO'),
                'customer_id' => $quotation->customer_id,
                'customer_project_id' => $quotation->customer_project_id,
                'quotation_id' => $quotation->id,
                'date' => now(),
                'status' => 'confirmed',
                'subtotal' => $quotation->subtotal,
                'discount_amount' => $quotation->discount_amount,
                'tax_amount' => $quotation->tax_amount,
                'total' => $quotation->total,
                'notes' => $quotation->notes,
                'created_by' => auth()->id(),
            ]);

            foreach ($quotation->items as $item) {
                $salesOrder->items()->create([
                    'product_id' => $item->product_id,
                    'unit_id' => $item->unit_id,
                    'qty' => $item->qty,
                    'price' => $item->price,
                    'discount_amount' => $item->discount_amount,
                    'subtotal' => $item->subtotal,
                ]);
            }

            $quotation->update(['status' => 'converted']);

            return $salesOrder;
        });

        return redirect()->route('sales-orders.show', $so)->with('success', 'Quotation berhasil dikonversi ke Sales Order.');
    }
}
