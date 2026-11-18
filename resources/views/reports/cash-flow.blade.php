<x-app-layout title="Laporan Cash Flow">
    <div class="row g-3 mb-3">
        <div class="col-md-4"><x-stat-card label="Kas Masuk" value="Rp {{ number_format($totalIn,0,',','.') }}" variant="mint" /></div>
        <div class="col-md-4"><x-stat-card label="Kas Keluar" value="Rp {{ number_format($totalOut,0,',','.') }}" variant="danger" /></div>
        <div class="col-md-4"><x-stat-card label="Net Cash Flow" value="Rp {{ number_format($totalIn-$totalOut,0,',','.') }}" variant="primary" /></div>
    </div>
    <x-card title="Cash Flow">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-5"><x-date-filter :from="$from" :to="$to" /></div>
            <div class="col-md-2"><x-field-button><button class="btn btn-outline btn-nb w-100">Filter</button></x-field-button></div>
        </form>
        @if($transactions->isEmpty())
            <x-empty-state title="Tidak ada mutasi pada periode ini" />
        @else
        <x-table>
            <thead><tr><th>Tanggal</th><th>Akun</th><th>Tipe</th><th>Deskripsi</th><th>Jumlah</th></tr></thead>
            <tbody>
                @foreach($transactions as $t)
                <tr>
                    <td class="small">{{ $t->transaction_date->format('d/m/Y') }}</td>
                    <td>{{ $t->cashAccount->name ?? '-' }}</td>
                    <td><x-badge :variant="str_contains($t->type,'in')?'mint':'danger'">{{ strtoupper($t->type) }}</x-badge></td>
                    <td class="small">{{ $t->description }}</td>
                    <td class="fw-bold">Rp {{ number_format($t->amount,0,',','.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        @endif
    </x-card>
</x-app-layout>
