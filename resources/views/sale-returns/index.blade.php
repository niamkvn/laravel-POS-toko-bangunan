<x-app-layout title="Retur Penjualan">
    <x-card title="Daftar Retur Penjualan">
        <x-slot:actions>@can('sales_return.create')<a href="{{ route('sale-returns.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Buat Retur</a>@endcan</x-slot:actions>
        @if($returns->isEmpty())
            <x-empty-state title="Belum ada retur penjualan" action-label="Buat Retur" :action-href="route('sale-returns.create')" />
        @else
        <x-table>
            <thead><tr><th>No. Retur</th><th>Invoice Asal</th><th>Customer</th><th>Tanggal</th><th>Total</th></tr></thead>
            <tbody>
                @foreach($returns as $r)
                <tr>
                    <td><a href="{{ route('sale-returns.show', $r) }}">{{ $r->return_number }}</a></td>
                    <td>{{ $r->sale->invoice_number ?? '-' }}</td>
                    <td>{{ $r->customer->name ?? 'Umum' }}</td>
                    <td class="small">{{ $r->return_date->format('d/m/Y') }}</td>
                    <td>Rp {{ number_format($r->total,0,',','.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$returns" />
        @endif
    </x-card>
</x-app-layout>
