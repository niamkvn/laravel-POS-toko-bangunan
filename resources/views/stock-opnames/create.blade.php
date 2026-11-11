<x-app-layout title="Buat Stock Opname">
    <x-card title="Buat Stock Opname" style="max-width:560px;">
        <form method="POST" action="{{ route('stock-opnames.store') }}">
            @csrf
            <div class="mb-3"><x-select label="Gudang" name="warehouse_id" :options="$warehouses" required /></div>
            <div class="mb-3"><x-input type="date" label="Tanggal" name="date" :value="now()->format('Y-m-d')" required /></div>
            <div class="mb-3"><x-input label="Catatan" name="notes" /></div>
            <p class="small text-muted">Sistem akan mengambil snapshot stok sistem untuk semua produk di gudang ini. Stok TIDAK akan berubah sampai opname diposting.</p>
            <button type="submit" class="btn btn-primary btn-nb">Mulai Opname</button>
            <a href="{{ route('stock-opnames.index') }}" class="btn btn-outline btn-nb">Batal</a>
        </form>
    </x-card>
</x-app-layout>
