<x-app-layout title="Retur Pembelian">
    <x-card title="Daftar Retur Pembelian">
        <x-slot:actions>@can('purchase_return.create')<a href="{{ route('purchase-returns.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Buat Retur</a>@endcan</x-slot:actions>
        @if($returns->isEmpty())
            <x-empty-state title="Belum ada retur pembelian" action-label="Buat Retur" :action-href="route('purchase-returns.create')" />
        @else
        <x-table>
            <thead><tr><th>No. Retur</th><th>No. Pembelian</th><th>Supplier</th><th>Tanggal</th><th>Total</th></tr></thead>
            <tbody>
                @foreach($returns as $r)
                <tr>
                    <td><a href="{{ route('purchase-returns.show', $r) }}">{{ $r->return_number }}</a></td>
                    <td>{{ $r->purchase->purchase_number ?? '-' }}</td>
                    <td>{{ $r->supplier->name ?? '-' }}</td>
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
