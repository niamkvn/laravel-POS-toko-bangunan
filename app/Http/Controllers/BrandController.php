<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $brands = Brand::when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->orderBy('name')->paginate(15)->withQueryString();

        return view('brands.index', compact('brands'));
    }

    public function create()
    {
        return view('brands.form', ['brand' => new Brand]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:150', 'active' => 'boolean']);
        $data['active'] = $request->boolean('active', true);
        Brand::create($data);

        return redirect()->route('brands.index')->with('success', 'Brand berhasil ditambahkan.');
    }

    public function edit(Brand $brand)
    {
        return view('brands.form', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $data = $request->validate(['name' => 'required|string|max:150', 'active' => 'boolean']);
        $data['active'] = $request->boolean('active', true);
        $brand->update($data);

        return redirect()->route('brands.index')->with('success', 'Brand berhasil diperbarui.');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->products()->exists()) {
            return back()->with('error', 'Brand tidak dapat dihapus karena masih digunakan.');
        }
        $brand->delete();

        return back()->with('success', 'Brand berhasil dihapus.');
    }
}
