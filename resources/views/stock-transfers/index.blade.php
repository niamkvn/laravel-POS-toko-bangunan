<x-app-layout title="Stock Transfer">
    <x-card title="Daftar Stock Transfer">
        <x-slot:actions>@can('stock.transfer')<a href="{{ route('stock-transfers.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Buat Transfer</a>@endcan</x-slot:actions>
        @if($transfers->isEmpty())
            <x-empty-state title="Belum ada stock transfer" action-label="Buat Transfer" :action-href="route('stock-transfers.create')" />
        @else
        <x-table>
            <thead><tr><th>No. Transfer</th><th>Dari</th><th>Ke</th><th>Tanggal</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($transfers as $t)
                <tr>
                    <td><a href="{{ route('stock-transfers.show', $t) }}">{{ $t->transfer_number }}</a></td>
                    <td>{{ $t->sourceWarehouse->name ?? '-' }}</td>
                    <td>{{ $t->destinationWarehouse->name ?? '-' }}</td>
                    <td class="small">{{ $t->transfer_date->format('d/m/Y') }}</td>
                    <td><x-badge :variant="$t->status==='received'?'mint':($t->status==='cancelled'?'danger':'soft')">{{ ucfirst(str_replace('_',' ',$t->status)) }}</x-badge></td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$transfers" />
        @endif
    </x-card>
</x-app-layout>
