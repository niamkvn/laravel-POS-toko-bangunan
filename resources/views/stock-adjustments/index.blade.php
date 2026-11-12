<x-app-layout title="Stock Adjustment">
    <x-card title="Daftar Stock Adjustment">
        <x-slot:actions>@can('stock.adjustment')<a href="{{ route('stock-adjustments.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Buat Adjustment</a>@endcan</x-slot:actions>
        @if($adjustments->isEmpty())
            <x-empty-state title="Belum ada stock adjustment" action-label="Buat Adjustment" :action-href="route('stock-adjustments.create')" />
        @else
        <x-table>
            <thead><tr><th>No.</th><th>Gudang</th><th>Tanggal</th><th>Alasan</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($adjustments as $a)
                <tr>
                    <td><a href="{{ route('stock-adjustments.show', $a) }}">{{ $a->adjustment_number }}</a></td>
                    <td>{{ $a->warehouse->name ?? '-' }}</td>
                    <td class="small">{{ $a->date->format('d/m/Y') }}</td>
                    <td class="small">{{ \Illuminate\Support\Str::limit($a->reason, 40) }}</td>
                    <td><x-badge :variant="$a->status==='posted'?'mint':($a->status==='pending'?'warning':'soft')">{{ ucfirst($a->status) }}</x-badge></td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$adjustments" />
        @endif
    </x-card>
</x-app-layout>
