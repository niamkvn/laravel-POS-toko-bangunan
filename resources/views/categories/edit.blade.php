<x-app-layout title="Edit Kategori">
    <x-card title="Edit Kategori" style="max-width:560px;">
        <form method="POST" action="{{ route('categories.update', $category) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <x-input label="Nama Kategori" name="name" :value="$category->name" required />
            </div>
            <div class="mb-3">
                <x-select label="Induk Kategori" name="parent_id" :options="$parents" :selected="$category->parent_id" placeholder="-- Tanpa Induk (Kategori Utama) --" />
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" name="active" value="1" class="form-check-input" id="active" @checked($category->active)>
                <label class="form-check-label" for="active">Aktif</label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-nb">Update</button>
                <a href="{{ route('categories.index') }}" class="btn btn-outline btn-nb">Batal</a>
            </div>
        </form>
    </x-card>
</x-app-layout>
