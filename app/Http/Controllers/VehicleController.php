<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $vehicles = Vehicle::with('driver')
            ->when($request->search, fn ($q) => $q->where('plate_number', 'like', "%{$request->search}%"))
            ->orderBy('vehicle_code')->paginate(15)->withQueryString();

        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $drivers = Driver::where('active', true)->pluck('name', 'id');

        return view('vehicles.form', ['vehicle' => new Vehicle, 'drivers' => $drivers]);
    }

    public function store(Request $request)
    {
        Vehicle::create($this->validated($request));

        return redirect()->route('vehicles.index')->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function edit(Vehicle $vehicle)
    {
        $drivers = Driver::where('active', true)->pluck('name', 'id');

        return view('vehicles.form', compact('vehicle', 'drivers'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $vehicle->update($this->validated($request, $vehicle->id));

        return redirect()->route('vehicles.index')->with('success', 'Kendaraan berhasil diperbarui.');
    }

    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->deliveryOrders()->exists()) {
            return back()->with('error', 'Kendaraan tidak dapat dihapus karena memiliki riwayat pengiriman.');
        }
        $vehicle->delete();

        return back()->with('success', 'Kendaraan berhasil dihapus.');
    }

    private function validated(Request $request, $id = null): array
    {
        $data = $request->validate([
            'vehicle_code' => 'required|string|max:20|unique:vehicles,vehicle_code'.($id ? ",{$id}" : ''),
            'plate_number' => 'required|string|max:20|unique:vehicles,plate_number'.($id ? ",{$id}" : ''),
            'vehicle_type' => 'required|string|max:50',
            'capacity' => 'nullable|string|max:50',
            'driver_id' => 'nullable|exists:drivers,id',
            'active' => 'boolean',
        ]);
        $data['active'] = $request->boolean('active', true);

        return $data;
    }
}
