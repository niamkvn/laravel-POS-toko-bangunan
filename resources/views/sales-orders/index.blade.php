<x-app-layout title="Sales Order">
    <x-card title="Daftar Sales Order">
        <x-slot:actions>@can('sales_order.create')<a href="{{ route('sales-orders.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Buat Sales Order</a>@endcan</x-slot:actions>
        @if($orders->isEmpty())
            <x-empty-state title="Belum ada sales order" action-label="Buat Sales Order" :action-href="route('sales-orders.create')" />
        @else
        <x-table>
            <thead><tr><th>No.</th><th>Customer</th><th>Tanggal</th><th>Total</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($orders as $so)
                <tr>
                    <td><a href="{{ route('sales-orders.show', $so) }}">{{ $so->number }}</a></td>
                    <td>{{ $so->customer->name ?? '-' }}</td>
                    <td class="small">{{ $so->date->format('d/m/Y') }}</td>
                    <td>Rp {{ number_format($so->total,0,',','.') }}</td>
                    <td><x-badge :variant="$so->status==='fulfilled'?'mint':($so->status==='cancelled'?'danger':'soft')">{{ ucfirst(str_replace('_',' ',$so->status)) }}</x-badge></td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$orders" />
        @endif
    </x-card>
</x-app-layout>
