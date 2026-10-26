<x-app-layout title="Detail Shift">
    <div class="row g-3 mb-3">
        <div class="col-md-3"><x-stat-card label="Saldo Awal" value="Rp {{ number_format($cashierShift->opening_balance,0,',','.') }}" variant="soft" /></div>
        <div class="col-md-3"><x-stat-card label="Penjualan Tunai" value="Rp {{ number_format($cashierShift->cash_sales,0,',','.') }}" variant="mint" /></div>
        <div class="col-md-3"><x-stat-card label="Expected Cash" value="Rp {{ number_format($cashierShift->expected_cash,0,',','.') }}" variant="primary" /></div>
        <div class="col-md-3"><x-stat-card label="Selisih" value="Rp {{ number_format($cashierShift->difference ?? 0,0,',','.') }}" :variant="($cashierShift->difference ?? 0) < 0 ? 'danger' : 'mint'" /></div>
    </div>

    <x-card title="Transaksi Selama Shift">
        @if($cashierShift->sales->isEmpty())
            <p class="text-muted small mb-0">Tidak ada transaksi.</p>
        @else
        <x-table>
            <thead><tr><th>Invoice</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($cashierShift->sales as $s)
                <tr>
                    <td><a href="{{ route('sales.show', $s) }}">{{ $s->invoice_number }}</a></td>
                    <td>{{ $s->customer->name ?? 'Umum' }}</td>
                    <td>Rp {{ number_format($s->total,0,',','.') }}</td>
                    <td><x-badge :variant="$s->status==='void'?'danger':'mint'">{{ ucfirst($s->status) }}</x-badge></td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        @endif
    </x-card>
</x-app-layout>
