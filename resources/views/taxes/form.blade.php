<x-app-layout :title="$tax->exists ? 'Edit Pajak' : 'Tambah Pajak'">
    <x-card :title="$tax->exists ? 'Edit Pajak' : 'Tambah Pajak'" style="max-width:480px;">
        <form method="POST" action="{{ $tax->exists ? route('taxes.update', $tax) : route('taxes.store') }}">
            @csrf
            @if($tax->exists) @method('PUT') @endif
            <div class="mb-3"><x-input label="Nama Pajak" name="name" :value="$tax->name" placeholder="PPN 11%" required /></div>
            <div class="mb-3"><x-input type="number" step="0.01" label="Rate (%)" name="rate" :value="$tax->rate" required /></div>
            <div class="form-check mb-2">
                <input type="checkbox" name="is_default" value="1" class="form-check-input" id="is_default" @checked($tax->is_default)>
                <label class="form-check-label" for="is_default">Jadikan default</label>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" name="active" value="1" class="form-check-input" id="active" @checked($tax->exists ? $tax->active : true)>
                <label class="form-check-label" for="active">Aktif</label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-nb">Simpan</button>
                <a href="{{ route('taxes.index') }}" class="btn btn-outline btn-nb">Batal</a>
            </div>
        </form>
    </x-card>
</x-app-layout>
