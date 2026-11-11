<x-app-layout :title="$stockOpname->opname_number">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0">Stock Opname #{{ $stockOpname->opname_number }}</h5>
            <p class="text-muted small mb-0">{{ $stockOpname->warehouse->name ?? '-' }} — {{ $stockOpname->date->format('d/m/Y') }} — Counter: {{ $stockOpname->counter->name ?? '-' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('stock-opnames.print', $stockOpname) }}" target="_blank" class="btn btn-outline btn-nb"><i class="bi bi-printer"></i> Cetak</a>
            <x-badge :variant="$stockOpname->status==='posted'?'mint':'soft'">{{ ucfirst($stockOpname->status) }}</x-badge>
            @if(in_array($stockOpname->status, ['counting']))
            <form method="POST" action="{{ route('stock-opnames.submit-review', $stockOpname) }}">@csrf<button class="btn btn-outline btn-nb">Submit Review</button></form>
            @endif
            @can('stock.opname.approve')
            @if($stockOpname->status === 'review')
            <form method="POST" action="{{ route('stock-opnames.approve', $stockOpname) }}">@csrf<button class="btn btn-deep btn-nb">Approve</button></form>
            @endif
            @if($stockOpname->status === 'approved')
            <form method="POST" action="{{ route('stock-opnames.post', $stockOpname) }}" onsubmit="return confirm('Posting akan mengubah stok sesuai selisih. Lanjutkan?')">@csrf<button class="btn btn-primary btn-nb">Post Opname</button></form>
            @endif
            @endcan
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-3"><x-stat-card label="Total SKU" value="{{ $totalSku }}" variant="soft" /></div>
        <div class="col-md-3"><x-stat-card label="Sudah Dihitung" value="{{ $counted }}/{{ $totalSku }}" variant="primary" /></div>
        <div class="col-md-3"><x-stat-card label="Selisih Naik/Turun" value="{{ $increase }} / {{ $decrease }}" variant="mint" /></div>
        <div class="col-md-3"><x-stat-card label="Nilai Selisih" value="Rp {{ number_format($differenceValue,0,',','.') }}" :variant="$differenceValue < 0 ? 'danger' : 'mint'" /></div>
    </div>

    <x-card title="Item Opname">
        <form method="POST" action="{{ route('stock-opnames.count', $stockOpname) }}">
            @csrf
            <x-table>
                <thead><tr><th>Produk</th><th>System</th><th>Physical</th><th>Selisih</th><th>Nilai</th><th>Alasan</th><th>Catatan</th></tr></thead>
                <tbody>
                    @foreach($stockOpname->items as $item)
                    @php
                        $rowClass = $item->difference_qty === null ? '' : ($item->difference_qty < 0 ? 'diff-negative' : ($item->difference_qty > 0 ? 'diff-positive' : 'diff-zero'));
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td>{{ $item->product->name ?? '-' }}
                            <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
                        </td>
                        <td>{{ $item->system_qty }}</td>
                        <td style="width:110px;">
                            <input type="number" step="0.001" name="items[{{ $loop->index }}][physical_qty]" value="{{ $item->physical_qty }}" class="form-control form-control-sm" {{ $stockOpname->status !== 'counting' ? 'disabled' : '' }}>
                        </td>
                        <td class="fw-bold">{{ $item->difference_qty ?? '-' }}</td>
                        <td>{{ $item->difference_value !== null ? 'Rp '.number_format($item->difference_value,0,',','.') : '-' }}</td>
                        <td style="width:150px;">
                            <select name="items[{{ $loop->index }}][reason]" class="form-select form-select-sm" {{ $stockOpname->status !== 'counting' ? 'disabled' : '' }}>
                                <option value="">-</option>
                                @foreach(['damaged'=>'Damaged','missing'=>'Missing','found'=>'Found','wrong_entry'=>'Wrong Entry','wrong_unit'=>'Wrong Unit','receiving_error'=>'Receiving Error','other'=>'Other'] as $k=>$v)
                                    <option value="{{ $k }}" @selected($item->reason===$k)>{{ $v }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td style="width:160px;">
                            <input type="text" name="items[{{ $loop->index }}][notes]" value="{{ $item->notes }}" class="form-control form-control-sm" {{ $stockOpname->status !== 'counting' ? 'disabled' : '' }}>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </x-table>
            @if($stockOpname->status === 'counting')
            <button type="submit" class="btn btn-primary btn-nb mt-3">Save Draft Penghitungan</button>
            @endif
        </form>
    </x-card>
</x-app-layout>
