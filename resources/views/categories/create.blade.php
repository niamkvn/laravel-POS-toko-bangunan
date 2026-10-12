<x-app-layout title="Tambah Kategori">
    <x-card title="Tambah Kategori" style="max-width:560px;">
        <form method="POST" action="{{ route('categories.store') }}">
            @csrf
            <div class="mb-3">
                <x-input label="Nama Kategori" name="name" required />
            </div>
            <div class="mb-3">
                <x-select label="Induk Kategori" name="parent_id" :options="$parents" placeholder="-- Tanpa Induk (Kategori Utama) --" />
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" name="active" value="1" class="form-check-input" id="active" checked>
                <label class="form-check-label" for="active">Aktif</label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-nb">Simpan</button>
                <a href="{{ route('categories.index') }}" class="btn btn-outline btn-nb">Batal</a>
            </div>
        </form>
    </x-card>
</x-app-layout>
