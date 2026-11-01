<x-app-layout title="Penjualan">
    <x-card title="Daftar Penjualan">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4"><x-search label="Cari" name="search" :value="request('search')" placeholder="Cari invoice..." /></div>
            <div class="col-md-3">
                <x-select label="Status" name="status" :options="['completed'=>'Completed','held'=>'Held','void'=>'Void']" :selected="request('status')" placeholder="Semua Status" auto-submit />
            </div>
            <div class="col-md-5"><x-date-filter :from="request('date_from')" :to="request('date_to')" /></div>
        </form>

        @if($sales->isEmpty())
            <x-empty-state title="Belum ada transaksi" action-label="Buka POS" :action-href="route('pos.index')" />
        @else
        <x-table>
            <thead><tr><th>Invoice</th><th>Tanggal</th><th>Customer</th><th>Total</th><th>Bayar</th><th>Tipe</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($sales as $s)
                <tr>
                    <td><a href="{{ route('sales.show', $s) }}">{{ $s->invoice_number }}</a></td>
                    <td class="small">{{ $s->sale_date->format('d/m/Y H:i') }}</td>
                    <td>{{ $s->customer->name ?? 'Umum' }}</td>
                    <td>Rp {{ number_format($s->total,0,',','.') }}</td>
                    <td>Rp {{ number_format($s->paid_amount,0,',','.') }}</td>
                    <td><x-badge :variant="$s->is_credit ? 'warning' : 'soft'">{{ $s->is_credit ? 'Kredit' : 'Tunai' }}</x-badge></td>
                    <td><x-badge :variant="$s->status==='void'?'danger':($s->status==='held'?'soft':'mint')">{{ ucfirst($s->status) }}</x-badge></td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$sales" />
        @endif
    </x-card>
</x-app-layout>
