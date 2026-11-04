<x-app-layout :title="$deliveryOrder->do_number">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0">{{ $deliveryOrder->do_number }}</h5>
            <p class="text-muted small mb-0">{{ $deliveryOrder->customer->name ?? '-' }} — {{ $deliveryOrder->delivery_date->format('d/m/Y') }}</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <a href="{{ route('delivery-orders.print', $deliveryOrder) }}" target="_blank" class="btn btn-outline btn-nb"><i class="bi bi-printer"></i> Cetak</a>
            <x-badge :variant="$deliveryOrder->status==='delivered'?'mint':'soft'">{{ ucfirst($deliveryOrder->status) }}</x-badge>
            @can('delivery.edit')<a href="{{ route('delivery-orders.edit', $deliveryOrder) }}" class="btn btn-outline btn-nb">Update Status</a>@endcan
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <x-card title="Item Dikirim">
                <x-table>
                    <thead><tr><th>Produk</th><th>Qty</th></tr></thead>
                    <tbody>
                        @foreach($deliveryOrder->items as $item)
                        <tr><td>{{ $item->product->name ?? '-' }}</td><td>{{ $item->qty }} {{ $item->unit->code ?? '' }}</td></tr>
                        @endforeach
                    </tbody>
                </x-table>
            </x-card>
        </div>
        <div class="col-lg-4">
            <x-card title="Info Pengiriman">
                <p class="mb-1 small"><strong>Driver:</strong> {{ $deliveryOrder->driver->name ?? '-' }}</p>
                <p class="mb-1 small"><strong>Kendaraan:</strong> {{ $deliveryOrder->vehicle->plate_number ?? '-' }}</p>
                <p class="mb-1 small"><strong>Alamat:</strong> {{ $deliveryOrder->delivery_address ?? '-' }}</p>
                <p class="mb-0 small"><strong>Invoice:</strong> <a href="{{ route('sales.show', $deliveryOrder->sale) }}">{{ $deliveryOrder->sale->invoice_number ?? '-' }}</a></p>
            </x-card>
        </div>
    </div>
</x-app-layout>
