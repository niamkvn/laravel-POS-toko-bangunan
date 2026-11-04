<x-app-layout title="Delivery Order">
    <x-card title="Daftar Pengiriman">
        <x-slot:actions>@can('delivery.create')<a href="{{ route('delivery-orders.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Buat DO</a>@endcan</x-slot:actions>
        <form method="GET" class="mb-3" style="max-width:280px;">
            <x-select name="status" :options="['pending'=>'Pending','preparing'=>'Preparing','shipped'=>'Shipped','delivered'=>'Delivered','cancelled'=>'Cancelled']" :selected="request('status')" placeholder="Semua Status" auto-submit />
        </form>
        @if($deliveries->isEmpty())
            <x-empty-state title="Belum ada pengiriman" action-label="Buat DO" :action-href="route('delivery-orders.create')" />
        @else
        <x-table>
            <thead><tr><th>No. DO</th><th>Customer</th><th>Driver</th><th>Kendaraan</th><th>Tanggal</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($deliveries as $d)
                <tr>
                    <td><a href="{{ route('delivery-orders.show', $d) }}">{{ $d->do_number }}</a></td>
                    <td>{{ $d->customer->name ?? '-' }}</td>
                    <td>{{ $d->driver->name ?? '-' }}</td>
                    <td>{{ $d->vehicle->plate_number ?? '-' }}</td>
                    <td class="small">{{ $d->delivery_date->format('d/m/Y') }}</td>
                    <td><x-badge :variant="$d->status==='delivered'?'mint':($d->status==='cancelled'?'danger':'soft')">{{ ucfirst($d->status) }}</x-badge></td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$deliveries" />
        @endif
    </x-card>
</x-app-layout>
