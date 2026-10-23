<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('supplier_code', 'like', "%{$request->search}%"))
            ->orderBy('name')->paginate(15)->withQueryString();

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.form', ['supplier' => new Supplier]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['supplier_code'] = 'SUPP-'.str_pad((Supplier::max('id') + 1), 5, '0', STR_PAD_LEFT);
        Supplier::create($data);

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function show(Supplier $supplier)
    {
        $purchases = $supplier->purchases()->latest('purchase_date')->paginate(10);

        return view('suppliers.show', compact('supplier', 'purchases'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.form', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $supplier->update($this->validated($request, $supplier->id));

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchases()->exists()) {
            return back()->with('error', 'Supplier tidak dapat dihapus karena memiliki riwayat transaksi.');
        }
        $supplier->delete();

        return back()->with('success', 'Supplier berhasil dihapus.');
    }

    public function payables(Request $request)
    {
        $purchases = Purchase::with('supplier')
            ->when($request->supplier_id, fn ($q) => $q->where('supplier_id', $request->supplier_id))
            ->orderBy('due_date')
            ->get()
            ->filter(fn ($p) => $p->outstanding() > 0);

        $suppliers = Supplier::orderBy('name')->pluck('name', 'id');

        return view('suppliers.payables', compact('purchases', 'suppliers'));
    }

    private function validated(Request $request, $id = null): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'company_name' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string',
            'tax_number' => 'nullable|string|max:50',
            'payment_term_days' => 'required|integer|min:0',
            'credit_limit' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'active' => 'boolean',
        ]);
        $data['active'] = $request->boolean('active', true);

        return $data;
    }
}
