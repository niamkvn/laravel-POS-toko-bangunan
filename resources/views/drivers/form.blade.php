<x-app-layout :title="$driver->exists ? 'Edit Driver' : 'Tambah Driver'">
    <x-card :title="$driver->exists ? 'Edit Driver' : 'Tambah Driver'" style="max-width:560px;">
        <form method="POST" action="{{ $driver->exists ? route('drivers.update', $driver) : route('drivers.store') }}">
            @csrf
            @if($driver->exists) @method('PUT') @endif
            <div class="mb-3"><x-input label="Kode Driver" name="driver_code" :value="$driver->driver_code" required /></div>
            <div class="mb-3"><x-input label="Nama" name="name" :value="$driver->name" required /></div>
            <div class="mb-3"><x-input label="Telepon" name="phone" :value="$driver->phone" /></div>
            <div class="mb-3"><x-input label="No. SIM" name="license_number" :value="$driver->license_number" /></div>
            <div class="form-check mb-3">
                <input type="checkbox" name="active" value="1" class="form-check-input" id="active" @checked($driver->exists ? $driver->active : true)>
                <label class="form-check-label" for="active">Aktif</label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-nb">Simpan</button>
                <a href="{{ route('drivers.index') }}" class="btn btn-outline btn-nb">Batal</a>
            </div>
        </form>
    </x-card>
</x-app-layout>
