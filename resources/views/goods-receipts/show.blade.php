<x-app-layout :title="$goodsReceipt->receipt_number">
    <div class="mb-3">
        <h5 class="mb-0">{{ $goodsReceipt->receipt_number }}</h5>
        <p class="text-muted small mb-0">{{ $goodsReceipt->supplier->name ?? '-' }} — {{ $goodsReceipt->warehouse->name ?? '-' }} — {{ $goodsReceipt->receipt_date->format('d/m/Y') }}</p>
    </div>

    <x-card title="Item Diterima">
        <x-table>
            <thead><tr><th>Produk</th><th>Qty</th><th>Rusak</th><th>Harga</th><th>Subtotal</th></tr></thead>
            <tbody>
                @foreach($goodsReceipt->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td>{{ $item->qty }} {{ $item->unit->code ?? '' }}</td>
                    <td>{{ $item->damaged_qty }}</td>
                    <td>Rp {{ number_format($item->unit_cost,0,',','.') }}</td>
                    <td>Rp {{ number_format($item->subtotal,0,',','.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
    </x-card>

    @if($goodsReceipt->purchase)
    <x-card title="Pembelian Terkait" class="mt-3">
        <a href="{{ route('purchases.show', $goodsReceipt->purchase) }}">{{ $goodsReceipt->purchase->purchase_number }}</a>
        — Total Rp {{ number_format($goodsReceipt->purchase->total,0,',','.') }}
    </x-card>
    @endif
</x-app-layout>
