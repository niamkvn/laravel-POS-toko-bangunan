<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $units = Unit::when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->orderBy('name')->paginate(20)->withQueryString();

        return view('units.index', compact('units'));
    }

    public function create()
    {
        return view('units.form', ['unit' => new Unit]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['code' => 'required|string|max:20|unique:units,code', 'name' => 'required|string|max:100']);
        Unit::create($data);

        return redirect()->route('units.index')->with('success', 'Satuan berhasil ditambahkan.');
    }

    public function edit(Unit $unit)
    {
        return view('units.form', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $data = $request->validate(['code' => "required|string|max:20|unique:units,code,{$unit->id}", 'name' => 'required|string|max:100']);
        $unit->update($data);

        return redirect()->route('units.index')->with('success', 'Satuan berhasil diperbarui.');
    }

    public function destroy(Unit $unit)
    {
        if ($unit->productUnits()->exists()) {
            return back()->with('error', 'Satuan tidak dapat dihapus karena masih digunakan.');
        }
        $unit->delete();

        return back()->with('success', 'Satuan berhasil dihapus.');
    }
}
