<x-app-layout title="Catat Mutasi Kas">
    <x-card title="Catat Mutasi Kas" style="max-width:560px;">
        <form method="POST" action="{{ route('cash-transactions.store') }}">
            @csrf
            <div class="mb-3"><x-select label="Kas / Bank" name="cash_account_id" :options="$cashAccounts->pluck('name','id')" required /></div>
            <div class="mb-3"><x-select label="Tipe" name="type" :options="['in'=>'Kas Masuk','out'=>'Kas Keluar']" required /></div>
            <div class="mb-3"><x-input type="number" step="0.01" label="Jumlah" name="amount" required /></div>
            <div class="mb-3"><x-input label="Kategori" name="category" placeholder="mis: modal, pinjaman, lainnya" /></div>
            <div class="mb-3"><x-input label="Deskripsi" name="description" required /></div>
            <div class="mb-3"><x-input type="date" label="Tanggal" name="transaction_date" :value="now()->format('Y-m-d')" required /></div>
            <button class="btn btn-primary btn-nb">Simpan</button>
            <a href="{{ route('cash-transactions.index') }}" class="btn btn-outline btn-nb">Batal</a>
        </form>
    </x-card>
</x-app-layout>
