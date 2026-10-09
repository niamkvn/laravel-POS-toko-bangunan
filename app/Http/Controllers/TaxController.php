<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function index()
    {
        $taxes = Tax::orderBy('name')->paginate(15);

        return view('taxes.index', compact('taxes'));
    }

    public function create()
    {
        return view('taxes.form', ['tax' => new Tax]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        if ($data['is_default']) {
            Tax::query()->update(['is_default' => false]);
        }
        Tax::create($data);

        return redirect()->route('taxes.index')->with('success', 'Pajak berhasil ditambahkan.');
    }

    public function edit(Tax $tax)
    {
        return view('taxes.form', compact('tax'));
    }

    public function update(Request $request, Tax $tax)
    {
        $data = $this->validated($request);
        if ($data['is_default']) {
            Tax::where('id', '!=', $tax->id)->update(['is_default' => false]);
        }
        $tax->update($data);

        return redirect()->route('taxes.index')->with('success', 'Pajak berhasil diperbarui.');
    }

    public function destroy(Tax $tax)
    {
        if ($tax->products()->exists()) {
            return back()->with('error', 'Pajak tidak dapat dihapus karena masih digunakan.');
        }
        $tax->delete();

        return back()->with('success', 'Pajak berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'rate' => 'required|numeric|min:0|max:100',
            'is_default' => 'boolean',
            'active' => 'boolean',
        ]);
        $data['is_default'] = $request->boolean('is_default');
        $data['active'] = $request->boolean('active', true);

        return $data;
    }
}
