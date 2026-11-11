<x-app-layout title="Stock Opname">
    <x-card title="Daftar Stock Opname">
        <x-slot:actions>@can('stock.opname')<a href="{{ route('stock-opnames.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Buat Opname</a>@endcan</x-slot:actions>
        @if($opnames->isEmpty())
            <x-empty-state title="Belum ada stock opname" action-label="Buat Opname" :action-href="route('stock-opnames.create')" />
        @else
        <x-table>
            <thead><tr><th>No. Opname</th><th>Gudang</th><th>Tanggal</th><th>Counter</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($opnames as $o)
                <tr>
                    <td><a href="{{ route('stock-opnames.show', $o) }}">{{ $o->opname_number }}</a></td>
                    <td>{{ $o->warehouse->name ?? '-' }}</td>
                    <td class="small">{{ $o->date->format('d/m/Y') }}</td>
                    <td>{{ $o->counter->name ?? '-' }}</td>
                    <td><x-badge :variant="$o->status==='posted'?'mint':($o->status==='cancelled'?'danger':'soft')">{{ ucfirst($o->status) }}</x-badge></td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$opnames" />
        @endif
    </x-card>
</x-app-layout>
