<x-app-layout :title="$quotation->number">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0">{{ $quotation->number }}</h5>
            <p class="text-muted small mb-0">{{ $quotation->customer->name ?? '-' }} — {{ $quotation->date->format('d/m/Y') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('quotations.print', $quotation) }}" target="_blank" class="btn btn-outline btn-nb"><i class="bi bi-printer"></i> Cetak</a>
            <x-badge :variant="$quotation->status==='converted'?'mint':'soft'">{{ ucfirst($quotation->status) }}</x-badge>
            @if($quotation->status === 'draft')
            <form method="POST" action="{{ route('quotations.send', $quotation) }}">@csrf<button class="btn btn-outline btn-nb">Tandai Terkirim</button></form>
            @endif
            @if($quotation->status === 'sent')
            <form method="POST" action="{{ route('quotations.approve', $quotation) }}">@csrf<button class="btn btn-deep btn-nb">Approved by Customer</button></form>
            <form method="POST" action="{{ route('quotations.reject', $quotation) }}">@csrf<button class="btn btn-danger btn-nb">Reject</button></form>
            @endif
            @if(in_array($quotation->status, ['approved','sent']))
            <form method="POST" action="{{ route('quotations.convert', $quotation) }}">@csrf<button class="btn btn-primary btn-nb">Convert ke Sales Order</button></form>
            @endif
        </div>
    </div>

    <x-card title="Item Quotation">
        <x-table>
            <thead><tr><th>Produk</th><th>Qty</th><th>Harga</th><th>Diskon</th><th>Subtotal</th></tr></thead>
            <tbody>
                @foreach($quotation->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td>{{ $item->qty }} {{ $item->unit->code ?? '' }}</td>
                    <td>Rp {{ number_format($item->price,0,',','.') }}</td>
                    <td>Rp {{ number_format($item->discount_amount,0,',','.') }}</td>
                    <td>Rp {{ number_format($item->subtotal,0,',','.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <div class="text-end mt-3"><h5>Total: Rp {{ number_format($quotation->total,0,',','.') }}</h5></div>
    </x-card>

    @if($quotation->salesOrder)
    <x-card title="Sales Order" class="mt-3">
        <a href="{{ route('sales-orders.show', $quotation->salesOrder) }}">{{ $quotation->salesOrder->number }}</a>
    </x-card>
    @endif
</x-app-layout>
