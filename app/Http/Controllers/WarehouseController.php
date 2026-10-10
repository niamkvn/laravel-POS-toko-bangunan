<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $warehouses = Warehouse::when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->orderBy('name')->paginate(15)->withQueryString();

        return view('warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        return view('warehouses.form', ['warehouse' => new Warehouse]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        if ($data['is_default']) {
            Warehouse::query()->update(['is_default' => false]);
        }
        Warehouse::create($data);

        return redirect()->route('warehouses.index')->with('success', 'Gudang berhasil ditambahkan.');
    }

    public function edit(Warehouse $warehouse)
    {
        return view('warehouses.form', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $data = $this->validated($request, $warehouse->id);
        if ($data['is_default']) {
            Warehouse::where('id', '!=', $warehouse->id)->update(['is_default' => false]);
        }
        $warehouse->update($data);

        return redirect()->route('warehouses.index')->with('success', 'Gudang berhasil diperbarui.');
    }

    public function destroy(Warehouse $warehouse)
    {
        if ($warehouse->stocks()->where('quantity', '>', 0)->exists()) {
            return back()->with('error', 'Gudang tidak dapat dihapus karena masih memiliki stok.');
        }
        $warehouse->delete();

        return back()->with('success', 'Gudang berhasil dihapus.');
    }

    private function validated(Request $request, $id = null): array
    {
        $data = $request->validate([
            'code' => 'required|string|max:20|unique:warehouses,code'.($id ? ",{$id}" : ''),
            'name' => 'required|string|max:150',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:30',
            'is_default' => 'boolean',
            'active' => 'boolean',
        ]);
        $data['is_default'] = $request->boolean('is_default');
        $data['active'] = $request->boolean('active', true);

        return $data;
    }
}
