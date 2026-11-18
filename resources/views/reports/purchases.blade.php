<x-app-layout title="Laporan Pembelian">
    <div class="row g-3 mb-3">
        <div class="col-md-6"><x-stat-card label="Total Pembelian" value="Rp {{ number_format($summary['total_purchases'],0,',','.') }}" variant="primary" /></div>
        <div class="col-md-6"><x-stat-card label="Jumlah Transaksi" value="{{ $summary['total_transactions'] }}" variant="soft" /></div>
    </div>

    <x-card title="Laporan Pembelian">
        <x-slot:actions><a href="{{ route('reports.purchases.export', request()->query()) }}" class="btn btn-outline btn-nb"><i class="bi bi-download"></i> Export CSV</a></x-slot:actions>
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-5"><x-date-filter :from="$from" :to="$to" /></div>
            <div class="col-md-3">
                <x-select label="Supplier" name="supplier_id" :options="$suppliers" :selected="request('supplier_id')" placeholder="Semua Supplier" />
            </div>
            <div class="col-md-2"><x-field-button><button class="btn btn-outline btn-nb w-100">Filter</button></x-field-button></div>
        </form>

        @if($purchases->isEmpty())
            <x-empty-state title="Tidak ada data pada periode ini" />
        @else
        <x-table>
            <thead><tr><th>No. Pembelian</th><th>Tanggal</th><th>Supplier</th><th>Total</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($purchases as $p)
                <tr>
                    <td><a href="{{ route('purchases.show', $p) }}">{{ $p->purchase_number }}</a></td>
                    <td class="small">{{ $p->purchase_date->format('d/m/Y') }}</td>
                    <td>{{ $p->supplier->name ?? '-' }}</td>
                    <td class="fw-bold">Rp {{ number_format($p->total,0,',','.') }}</td>
                    <td><x-badge :variant="$p->status==='paid'?'mint':'warning'">{{ ucfirst($p->status) }}</x-badge></td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        @endif
    </x-card>
</x-app-layout>
