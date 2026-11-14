<x-app-layout title="Catat Pengeluaran">
    <x-card title="Catat Pengeluaran" style="max-width:560px;">
        <form method="POST" action="{{ route('expenses.store') }}">
            @csrf
            <div class="mb-3"><x-select label="Kategori" name="expense_category_id" :options="$categories" required /></div>
            <div class="mb-3"><x-select label="Kas / Bank" name="cash_account_id" :options="$cashAccounts" required /></div>
            <div class="mb-3"><x-input type="number" step="0.01" label="Jumlah" name="amount" required /></div>
            <div class="mb-3"><x-input type="date" label="Tanggal" name="expense_date" :value="now()->format('Y-m-d')" required /></div>
            <div class="mb-3"><x-input label="Deskripsi" name="description" required /></div>
            <button class="btn btn-primary btn-nb">Simpan</button>
            <a href="{{ route('expenses.index') }}" class="btn btn-outline btn-nb">Batal</a>
        </form>
    </x-card>
</x-app-layout>
