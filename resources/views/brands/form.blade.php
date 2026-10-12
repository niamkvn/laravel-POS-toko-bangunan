<x-app-layout :title="$brand->exists ? 'Edit Brand' : 'Tambah Brand'">
    <x-card :title="$brand->exists ? 'Edit Brand' : 'Tambah Brand'" style="max-width:560px;">
        <form method="POST" action="{{ $brand->exists ? route('brands.update', $brand) : route('brands.store') }}">
            @csrf
            @if($brand->exists) @method('PUT') @endif
            <div class="mb-3"><x-input label="Nama Brand" name="name" :value="$brand->name" required /></div>
            <div class="form-check mb-3">
                <input type="checkbox" name="active" value="1" class="form-check-input" id="active" @checked($brand->exists ? $brand->active : true)>
                <label class="form-check-label" for="active">Aktif</label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-nb">Simpan</button>
                <a href="{{ route('brands.index') }}" class="btn btn-outline btn-nb">Batal</a>
            </div>
        </form>
    </x-card>
</x-app-layout>
