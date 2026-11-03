<x-app-layout :title="$saleReturn->return_number">
    <div class="mb-3">
        <h5 class="mb-0">{{ $saleReturn->return_number }}</h5>
        <p class="text-muted small mb-0">Invoice: <a href="{{ route('sales.show', $saleReturn->sale) }}">{{ $saleReturn->sale->invoice_number ?? '-' }}</a> — {{ $saleReturn->return_date->format('d/m/Y') }}</p>
    </div>

    <x-card title="Item Diretur">
        <x-table>
            <thead><tr><th>Produk</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr></thead>
            <tbody>
                @foreach($saleReturn->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td>{{ $item->qty }} {{ $item->unit->code ?? '' }}</td>
                    <td>Rp {{ number_format($item->price,0,',','.') }}</td>
                    <td>Rp {{ number_format($item->subtotal,0,',','.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="text-end mt-3"><h5>Total Retur: Rp {{ number_format($saleReturn->total,0,',','.') }}</h5></div>
    </x-card>
    @if($saleReturn->reason)
    <x-card title="Alasan" class="mt-3"><p class="mb-0">{{ $saleReturn->reason }}</p></x-card>
    @endif
</x-app-layout>
