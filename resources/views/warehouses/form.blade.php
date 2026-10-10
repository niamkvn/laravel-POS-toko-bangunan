<x-app-layout :title="$warehouse->exists ? 'Edit Gudang' : 'Tambah Gudang'">
    <x-card :title="$warehouse->exists ? 'Edit Gudang' : 'Tambah Gudang'" style="max-width:560px;">
        <form method="POST" action="{{ $warehouse->exists ? route('warehouses.update', $warehouse) : route('warehouses.store') }}">
            @csrf
            @if($warehouse->exists) @method('PUT') @endif
            <div class="mb-3"><x-input label="Kode Gudang" name="code" :value="$warehouse->code" required /></div>
            <div class="mb-3"><x-input label="Nama Gudang" name="name" :value="$warehouse->name" required /></div>
            <div class="mb-3"><x-input label="Alamat" name="address" :value="$warehouse->address" /></div>
            <div class="mb-3"><x-input label="Telepon" name="phone" :value="$warehouse->phone" /></div>
            <div class="form-check mb-2">
                <input type="checkbox" name="is_default" value="1" class="form-check-input" id="is_default" @checked($warehouse->is_default)>
                <label class="form-check-label" for="is_default">Jadikan gudang default</label>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" name="active" value="1" class="form-check-input" id="active" @checked($warehouse->exists ? $warehouse->active : true)>
                <label class="form-check-label" for="active">Aktif</label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-nb">Simpan</button>
                <a href="{{ route('warehouses.index') }}" class="btn btn-outline btn-nb">Batal</a>
            </div>
        </form>
    </x-card>
</x-app-layout>
