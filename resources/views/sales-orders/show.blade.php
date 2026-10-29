<x-app-layout :title="$salesOrder->number">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0">{{ $salesOrder->number }}</h5>
            <p class="text-muted small mb-0">{{ $salesOrder->customer->name ?? '-' }} — {{ $salesOrder->date->format('d/m/Y') }}</p>
        </div>
        <div class="d-flex gap-2">
            <x-badge :variant="$salesOrder->status==='fulfilled'?'mint':'soft'">{{ ucfirst(str_replace('_',' ',$salesOrder->status)) }}</x-badge>
            @if($salesOrder->status !== 'fulfilled' && $salesOrder->status !== 'cancelled')
            <a href="{{ route('sales-orders.fulfill-form', $salesOrder) }}" class="btn btn-primary btn-nb">Fulfill / Kirim</a>
            @endif
        </div>
    </div>

    <x-card title="Item Order">
        <x-table>
            <thead><tr><th>Produk</th><th>Qty Order</th><th>Terpenuhi</th><th>Sisa</th><th>Harga</th></tr></thead>
            <tbody>
                @foreach($salesOrder->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td>{{ $item->qty }} {{ $item->unit->code ?? '' }}</td>
                    <td>{{ $item->fulfilled_qty }}</td>
                    <td class="fw-bold">{{ $item->remainingQty() }}</td>
                    <td>Rp {{ number_format($item->price,0,',','.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="text-end mt-3"><h5>Total: Rp {{ number_format($salesOrder->total,0,',','.') }}</h5></div>
    </x-card>

    @if($salesOrder->sales->isNotEmpty())
    <x-card title="Riwayat Pengiriman / Invoice" class="mt-3">
        @foreach($salesOrder->sales as $sale)
        <div class="d-flex justify-content-between border-bottom py-2">
            <a href="{{ route('sales.show', $sale) }}">{{ $sale->invoice_number }}</a>
            <span class="small text-muted">Rp {{ number_format($sale->total,0,',','.') }}</span>
        </div>
        @endforeach
    </x-card>
    @endif
</x-app-layout>
