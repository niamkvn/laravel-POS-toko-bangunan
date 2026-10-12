<x-app-layout :title="$unit->exists ? 'Edit Satuan' : 'Tambah Satuan'">
    <x-card :title="$unit->exists ? 'Edit Satuan' : 'Tambah Satuan'" style="max-width:480px;">
        <form method="POST" action="{{ $unit->exists ? route('units.update', $unit) : route('units.store') }}">
            @csrf
            @if($unit->exists) @method('PUT') @endif
            <div class="mb-3"><x-input label="Kode Satuan" name="code" :value="$unit->code" placeholder="PCS, DUS, SAK, KG..." required /></div>
            <div class="mb-3"><x-input label="Nama Satuan" name="name" :value="$unit->name" placeholder="Pieces, Dus, Sak..." required /></div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-nb">Simpan</button>
                <a href="{{ route('units.index') }}" class="btn btn-outline btn-nb">Batal</a>
            </div>
        </form>
    </x-card>
</x-app-layout>
