<?php

namespace App\Http\Controllers;

use App\Models\PriceLevel;
use Illuminate\Http\Request;

class PriceLevelController extends Controller
{
    public function index()
    {
        $priceLevels = PriceLevel::orderBy('name')->paginate(15);

        return view('price-levels.index', compact('priceLevels'));
    }

    public function create()
    {
        return view('price-levels.form', ['priceLevel' => new PriceLevel]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        if ($data['is_default']) {
            PriceLevel::query()->update(['is_default' => false]);
        }
        PriceLevel::create($data);

        return redirect()->route('price-levels.index')->with('success', 'Price level berhasil ditambahkan.');
    }

    public function edit(PriceLevel $priceLevel)
    {
        return view('price-levels.form', compact('priceLevel'));
    }

    public function update(Request $request, PriceLevel $priceLevel)
    {
        $data = $this->validated($request, $priceLevel->id);
        if ($data['is_default']) {
            PriceLevel::where('id', '!=', $priceLevel->id)->update(['is_default' => false]);
        }
        $priceLevel->update($data);

        return redirect()->route('price-levels.index')->with('success', 'Price level berhasil diperbarui.');
    }

    public function destroy(PriceLevel $priceLevel)
    {
        if ($priceLevel->customers()->exists()) {
            return back()->with('error', 'Price level tidak dapat dihapus karena masih digunakan customer.');
        }
        $priceLevel->delete();

        return back()->with('success', 'Price level berhasil dihapus.');
    }

    private function validated(Request $request, $id = null): array
    {
        $data = $request->validate([
            'code' => 'required|string|max:20|unique:price_levels,code'.($id ? ",{$id}" : ''),
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_default' => 'boolean',
        ]);
        $data['is_default'] = $request->boolean('is_default');

        return $data;
    }
}
