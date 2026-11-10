<x-app-layout :title="$stockTransfer->transfer_number">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0">{{ $stockTransfer->transfer_number }}</h5>
            <p class="text-muted small mb-0">{{ $stockTransfer->sourceWarehouse->name ?? '-' }} → {{ $stockTransfer->destinationWarehouse->name ?? '-' }}</p>
        </div>
        <div class="d-flex gap-2">
            <x-badge :variant="$stockTransfer->status==='received'?'mint':'soft'">{{ ucfirst(str_replace('_',' ',$stockTransfer->status)) }}</x-badge>
            @if($stockTransfer->status === 'draft')
            <form method="POST" action="{{ route('stock-transfers.approve', $stockTransfer) }}">@csrf<button class="btn btn-outline btn-nb">Approve</button></form>
            @endif
            @if($stockTransfer->status === 'approved')
            <form method="POST" action="{{ route('stock-transfers.ship', $stockTransfer) }}">@csrf<button class="btn btn-primary btn-nb">Kirim (Kurangi Stok Asal)</button></form>
            @endif
            @if($stockTransfer->status === 'shipped')
            <form method="POST" action="{{ route('stock-transfers.receive', $stockTransfer) }}">@csrf<button class="btn btn-deep btn-nb">Terima di Tujuan (Tambah Stok)</button></form>
            @endif
        </div>
    </div>

    <x-card title="Item Transfer">
        <x-table>
            <thead><tr><th>Produk</th><th>Qty</th><th>Diterima</th></tr></thead>
            <tbody>
                @foreach($stockTransfer->items as $item)
                <tr><td>{{ $item->product->name ?? '-' }}</td><td>{{ $item->qty }} {{ $item->unit->code ?? '' }}</td><td>{{ $item->received_qty }}</td></tr>
                @endforeach
            </tbody>
        </x-table>
    </x-card>
</x-app-layout>
