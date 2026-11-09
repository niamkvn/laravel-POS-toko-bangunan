<x-app-layout :title="$purchaseReturn->return_number">
    <div class="mb-3">
        <h5 class="mb-0">{{ $purchaseReturn->return_number }}</h5>
        <p class="text-muted small mb-0">Pembelian: <a href="{{ route('purchases.show', $purchaseReturn->purchase) }}">{{ $purchaseReturn->purchase->purchase_number ?? '-' }}</a> — {{ $purchaseReturn->return_date->format('d/m/Y') }}</p>
    </div>

    <x-card title="Item Diretur">
        <x-table>
            <thead><tr><th>Produk</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr></thead>
            <tbody>
                @foreach($purchaseReturn->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td>{{ $item->qty }} {{ $item->unit->code ?? '' }}</td>
                    <td>Rp {{ number_format($item->unit_cost,0,',','.') }}</td>
                    <td>Rp {{ number_format($item->subtotal,0,',','.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="text-end mt-3"><h5>Total Retur: Rp {{ number_format($purchaseReturn->total,0,',','.') }}</h5></div>
    </x-card>
</x-app-layout>
