<x-app-layout :title="$priceLevel->exists ? 'Edit Price Level' : 'Tambah Price Level'">
    <x-card :title="$priceLevel->exists ? 'Edit Price Level' : 'Tambah Price Level'" style="max-width:560px;">
        <form method="POST" action="{{ $priceLevel->exists ? route('price-levels.update', $priceLevel) : route('price-levels.store') }}">
            @csrf
            @if($priceLevel->exists) @method('PUT') @endif
            <div class="mb-3"><x-input label="Kode" name="code" :value="$priceLevel->code" placeholder="UMUM, TUKANG, KONTRAKTOR, GROSIR" required /></div>
            <div class="mb-3"><x-input label="Nama" name="name" :value="$priceLevel->name" required /></div>
            <div class="mb-3"><x-input label="Deskripsi" name="description" :value="$priceLevel->description" /></div>
            <div class="form-check mb-3">
                <input type="checkbox" name="is_default" value="1" class="form-check-input" id="is_default" @checked($priceLevel->is_default)>
                <label class="form-check-label" for="is_default">Jadikan default</label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-nb">Simpan</button>
                <a href="{{ route('price-levels.index') }}" class="btn btn-outline btn-nb">Batal</a>
            </div>
        </form>
    </x-card>
</x-app-layout>
