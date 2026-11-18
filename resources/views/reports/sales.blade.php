<x-app-layout title="Laporan Penjualan">
    <div class="row g-3 mb-3">
        <div class="col-md-4"><x-stat-card label="Total Penjualan" value="Rp {{ number_format($summary['total_sales'],0,',','.') }}" variant="primary" /></div>
        <div class="col-md-4"><x-stat-card label="Jumlah Transaksi" value="{{ $summary['total_transactions'] }}" variant="soft" /></div>
        <div class="col-md-4"><x-stat-card label="Rata-rata / Transaksi" value="Rp {{ number_format($summary['average'],0,',','.') }}" variant="mint" /></div>
    </div>

    <x-card title="Laporan Penjualan">
        <x-slot:actions><a href="{{ route('reports.sales.export', request()->query()) }}" class="btn btn-outline btn-nb"><i class="bi bi-download"></i> Export CSV</a></x-slot:actions>
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-5"><x-date-filter :from="$from" :to="$to" /></div>
            <div class="col-md-3">
                <x-select label="Customer" name="customer_id" :options="$customers" :selected="request('customer_id')" placeholder="Semua Customer" />
            </div>
            <div class="col-md-2"><x-field-button><button class="btn btn-outline btn-nb w-100">Filter</button></x-field-button></div>
        </form>

        @if($sales->isEmpty())
            <x-empty-state title="Tidak ada data pada periode ini" />
        @else
        <x-table>
            <thead><tr><th>Invoice</th><th>Tanggal</th><th>Customer</th><th>Subtotal</th><th>Diskon</th><th>Total</th></tr></thead>
            <tbody>
                @foreach($sales as $s)
                <tr>
                    <td><a href="{{ route('sales.show', $s) }}">{{ $s->invoice_number }}</a></td>
                    <td class="small">{{ $s->sale_date->format('d/m/Y H:i') }}</td>
                    <td>{{ $s->customer->name ?? 'Umum' }}</td>
                    <td>Rp {{ number_format($s->subtotal,0,',','.') }}</td>
                    <td>Rp {{ number_format($s->discount_amount,0,',','.') }}</td>
                    <td class="fw-bold">Rp {{ number_format($s->total,0,',','.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        @endif
    </x-card>
</x-app-layout>
