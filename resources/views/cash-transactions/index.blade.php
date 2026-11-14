<x-app-layout title="Mutasi Kas">
    <x-card title="Mutasi Kas">
        <x-slot:actions>@can('cash.create')<a href="{{ route('cash-transactions.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Catat Mutasi</a>@endcan</x-slot:actions>

        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <x-select label="Akun" name="cash_account_id" :options="$cashAccounts" :selected="request('cash_account_id')" placeholder="Semua Akun" auto-submit />
            </div>
            <div class="col-md-2">
                <x-select label="Tipe" name="type" :options="['in'=>'Masuk','out'=>'Keluar']" :selected="request('type')" placeholder="Semua Tipe" auto-submit />
            </div>
            <div class="col-md-5"><x-date-filter :from="request('date_from')" :to="request('date_to')" /></div>
        </form>

        @if($transactions->isEmpty())
            <x-empty-state title="Belum ada mutasi kas" />
        @else
        <x-table>
            <thead><tr><th>Tanggal</th><th>Akun</th><th>Tipe</th><th>Kategori</th><th>Deskripsi</th><th>Jumlah</th></tr></thead>
            <tbody>
                @foreach($transactions as $t)
                <tr>
                    <td class="small">{{ $t->transaction_date->format('d/m/Y') }}</td>
                    <td>{{ $t->cashAccount->name ?? '-' }}</td>
                    <td><x-badge :variant="str_contains($t->type,'in') ? 'mint':'danger'">{{ strtoupper($t->type) }}</x-badge></td>
                    <td class="small">{{ $t->category }}</td>
                    <td class="small">{{ $t->description }}</td>
                    <td class="fw-bold">Rp {{ number_format($t->amount,0,',','.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$transactions" />
        @endif
    </x-card>
</x-app-layout>
