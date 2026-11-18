<x-app-layout title="Laporan Stock Opname">
    <x-card title="Riwayat Stock Opname">
        <x-table>
            <thead><tr><th>No.</th><th>Gudang</th><th>Tanggal</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($opnames as $o)
                <tr>
                    <td><a href="{{ route('stock-opnames.show', $o) }}">{{ $o->opname_number }}</a></td>
                    <td>{{ $o->warehouse->name ?? '-' }}</td>
                    <td class="small">{{ $o->date->format('d/m/Y') }}</td>
                    <td><x-badge :variant="$o->status==='posted'?'mint':'soft'">{{ ucfirst($o->status) }}</x-badge></td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$opnames" />
    </x-card>
</x-app-layout>
