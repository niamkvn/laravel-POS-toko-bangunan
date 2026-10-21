<x-app-layout :title="$customerProject->name">
    <div class="row g-3 mb-3">
        <div class="col-md-3"><x-stat-card label="Total Sales" value="Rp {{ number_format($totalSales,0,',','.') }}" variant="primary" /></div>
        <div class="col-md-3"><x-stat-card label="Total Payment" value="Rp {{ number_format($totalPaid,0,',','.') }}" variant="mint" /></div>
        <div class="col-md-3"><x-stat-card label="Outstanding" value="Rp {{ number_format($outstanding,0,',','.') }}" variant="warning" /></div>
        <div class="col-md-3"><x-stat-card label="Budget" value="Rp {{ number_format($customerProject->budget ?? 0,0,',','.') }}" variant="soft" /></div>
    </div>

    <x-card :title="$customerProject->name" :subtitle="$customerProject->project_code . ' — ' . ($customerProject->customer->name ?? '-')">
        <p class="mb-1"><strong>Lokasi:</strong> {{ $customerProject->location ?? '-' }}</p>
        <p class="mb-1"><strong>Periode:</strong> {{ $customerProject->start_date?->format('d/m/Y') }} - {{ $customerProject->end_date?->format('d/m/Y') ?? 'Berjalan' }}</p>
        <p class="mb-0"><strong>Status:</strong> <x-badge>{{ ucfirst($customerProject->status) }}</x-badge></p>
    </x-card>

    <x-card title="Transaksi Penjualan" class="mt-3">
        @if($customerProject->sales->isEmpty())
            <p class="text-muted small mb-0">Belum ada transaksi.</p>
        @else
        <x-table>
            <thead><tr><th>Invoice</th><th>Tanggal</th><th>Total</th></tr></thead>
            <tbody>
                @foreach($customerProject->sales as $s)
                <tr><td><a href="{{ route('sales.show', $s) }}">{{ $s->invoice_number }}</a></td><td>{{ $s->sale_date->format('d/m/Y') }}</td><td>Rp {{ number_format($s->total,0,',','.') }}</td></tr>
                @endforeach
            </tbody>
        </x-table>
        @endif
    </x-card>
</x-app-layout>
