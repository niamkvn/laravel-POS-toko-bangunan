<x-app-layout :title="$purchaseOrder->po_number">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0">{{ $purchaseOrder->po_number }}</h5>
            <p class="text-muted small mb-0">{{ $purchaseOrder->supplier->name ?? '-' }} — {{ $purchaseOrder->order_date->format('d/m/Y') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('purchase-orders.print', $purchaseOrder) }}" target="_blank" class="btn btn-outline btn-nb"><i class="bi bi-printer"></i> Cetak</a>
            <x-badge :variant="$purchaseOrder->status==='received'?'mint':'soft'">{{ ucfirst(str_replace('_',' ',$purchaseOrder->status)) }}</x-badge>
            @can('purchase_order.edit')
            @if($purchaseOrder->status === 'draft')
            <form method="POST" action="{{ route('purchase-orders.submit', $purchaseOrder) }}">@csrf<button class="btn btn-outline btn-nb">Ajukan Approval</button></form>
            @endif
            @endcan
            @can('purchase_order.approve')
            @if($purchaseOrder->status === 'submitted')
            <form method="POST" action="{{ route('purchase-orders.approve', $purchaseOrder) }}">@csrf<button class="btn btn-deep btn-nb">Approve PO</button></form>
            @endif
            @endcan
            @can('goods_receipt.create')
            @if(in_array($purchaseOrder->status, ['approved','partially_received']))
            <a href="{{ route('goods-receipts.create', ['purchase_order_id' => $purchaseOrder->id]) }}" class="btn btn-primary btn-nb">Terima Barang</a>
            @endif
            @endcan
        </div>
    </div>

    <x-card title="Item Pesanan">
        <x-table>
            <thead><tr><th>Produk</th><th>Qty</th><th>Diterima</th><th>Harga</th><th>Subtotal</th></tr></thead>
            <tbody>
                @foreach($purchaseOrder->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td>{{ $item->qty }} {{ $item->unit->code ?? '' }}</td>
                    <td>{{ $item->received_qty }}</td>
                    <td>Rp {{ number_format($item->price,0,',','.') }}</td>
                    <td>Rp {{ number_format($item->subtotal,0,',','.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="text-end mt-3"><h5>Total: Rp {{ number_format($purchaseOrder->total,0,',','.') }}</h5></div>
    </x-card>

    @if($purchaseOrder->goodsReceipts->isNotEmpty())
    <x-card title="Penerimaan Barang" class="mt-3">
        @foreach($purchaseOrder->goodsReceipts as $gr)
        <div class="d-flex justify-content-between border-bottom py-2">
            <a href="{{ route('goods-receipts.show', $gr) }}">{{ $gr->receipt_number }}</a>
            <span class="small text-muted">{{ $gr->receipt_date->format('d/m/Y') }}</span>
        </div>
        @endforeach
    </x-card>
    @endif
</x-app-layout>
