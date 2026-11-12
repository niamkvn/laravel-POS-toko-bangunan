<x-app-layout :title="$stockAdjustment->adjustment_number">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0">{{ $stockAdjustment->adjustment_number }}</h5>
            <p class="text-muted small mb-0">{{ $stockAdjustment->warehouse->name ?? '-' }} — {{ $stockAdjustment->date->format('d/m/Y') }}</p>
        </div>
        <div class="d-flex gap-2">
            <x-badge :variant="$stockAdjustment->status==='posted'?'mint':'warning'">{{ ucfirst($stockAdjustment->status) }}</x-badge>
            @can('stock.adjustment')
            @if($stockAdjustment->status === 'pending')
            <form method="POST" action="{{ route('stock-adjustments.approve', $stockAdjustment) }}">@csrf<button class="btn btn-deep btn-nb">Approve &amp; Post</button></form>
            @endif
            @endcan
        </div>
    </div>

    <x-card title="Alasan"><p class="mb-0">{{ $stockAdjustment->reason }}</p></x-card>

    <x-card title="Item Adjustment" class="mt-3">
        <x-table>
            <thead><tr><th>Produk</th><th>Qty</th><th>Unit Cost</th><th>Catatan</th></tr></thead>
            <tbody>
                @foreach($stockAdjustment->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td class="fw-bold {{ $item->adjustment_qty < 0 ? 'text-danger' : 'text-success' }}">{{ $item->adjustment_qty > 0 ? '+' : '' }}{{ $item->adjustment_qty }}</td>
                    <td>Rp {{ number_format($item->unit_cost,0,',','.') }}</td>
                    <td class="small">{{ $item->notes }}</td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
    </x-card>
</x-app-layout>
