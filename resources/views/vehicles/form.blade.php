<x-app-layout :title="$vehicle->exists ? 'Edit Kendaraan' : 'Tambah Kendaraan'">
    <x-card :title="$vehicle->exists ? 'Edit Kendaraan' : 'Tambah Kendaraan'" style="max-width:560px;">
        <form method="POST" action="{{ $vehicle->exists ? route('vehicles.update', $vehicle) : route('vehicles.store') }}">
            @csrf
            @if($vehicle->exists) @method('PUT') @endif
            <div class="mb-3"><x-input label="Kode Kendaraan" name="vehicle_code" :value="$vehicle->vehicle_code" required /></div>
            <div class="mb-3"><x-input label="Plat Nomor" name="plate_number" :value="$vehicle->plate_number" required /></div>
            <div class="mb-3"><x-input label="Tipe" name="vehicle_type" :value="$vehicle->vehicle_type" placeholder="Pickup, L300, Truck Engkel..." required /></div>
            <div class="mb-3"><x-input label="Kapasitas" name="capacity" :value="$vehicle->capacity" placeholder="1 Ton, 3 Ton..." /></div>
            <div class="mb-3"><x-select label="Driver" name="driver_id" :options="$drivers" :selected="$vehicle->driver_id" /></div>
            <div class="form-check mb-3">
                <input type="checkbox" name="active" value="1" class="form-check-input" id="active" @checked($vehicle->exists ? $vehicle->active : true)>
                <label class="form-check-label" for="active">Aktif</label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-nb">Simpan</button>
                <a href="{{ route('vehicles.index') }}" class="btn btn-outline btn-nb">Batal</a>
            </div>
        </form>
    </x-card>
</x-app-layout>
