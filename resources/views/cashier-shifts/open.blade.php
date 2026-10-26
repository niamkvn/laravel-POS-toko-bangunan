<x-app-layout title="Buka Shift Kasir">
    <x-card title="Buka Shift Kasir" style="max-width:480px;">
        <form method="POST" action="{{ route('cashier-shifts.open') }}">
            @csrf
            <div class="mb-3"><x-select label="Kas / Laci" name="cash_account_id" :options="$cashAccounts->pluck('name','id')" required /></div>
            <div class="mb-3"><x-input type="number" step="0.01" label="Saldo Awal (Modal Kasir)" name="opening_balance" value="0" required /></div>
            <button class="btn btn-deep btn-nb w-100">Buka Shift &amp; Mulai POS</button>
        </form>
    </x-card>
</x-app-layout>
