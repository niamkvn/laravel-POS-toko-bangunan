<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $drivers = Driver::when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->orderBy('name')->paginate(15)->withQueryString();

        return view('drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('drivers.form', ['driver' => new Driver]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Driver::create($data);

        return redirect()->route('drivers.index')->with('success', 'Driver berhasil ditambahkan.');
    }

    public function edit(Driver $driver)
    {
        return view('drivers.form', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $data = $this->validated($request, $driver->id);
        $driver->update($data);

        return redirect()->route('drivers.index')->with('success', 'Driver berhasil diperbarui.');
    }

    public function destroy(Driver $driver)
    {
        if ($driver->deliveryOrders()->exists()) {
            return back()->with('error', 'Driver tidak dapat dihapus karena memiliki riwayat pengiriman.');
        }
        $driver->delete();

        return back()->with('success', 'Driver berhasil dihapus.');
    }

    private function validated(Request $request, $id = null): array
    {
        $data = $request->validate([
            'driver_code' => 'required|string|max:20|unique:drivers,driver_code'.($id ? ",{$id}" : ''),
            'name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:30',
            'license_number' => 'nullable|string|max:50',
            'active' => 'boolean',
        ]);
        $data['active'] = $request->boolean('active', true);

        return $data;
    }
}
