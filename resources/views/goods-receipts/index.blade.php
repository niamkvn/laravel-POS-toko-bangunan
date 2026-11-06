<x-app-layout title="Penerimaan Barang">
    <x-card title="Daftar Penerimaan Barang">
        <x-slot:actions>@can('goods_receipt.create')<a href="{{ route('goods-receipts.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Terima Barang</a>@endcan</x-slot:actions>
        @if($receipts->isEmpty())
            <x-empty-state title="Belum ada penerimaan barang" action-label="Terima Barang" :action-href="route('goods-receipts.create')" />
        @else
        <x-table>
            <thead><tr><th>No. Penerimaan</th><th>Supplier</th><th>Gudang</th><th>Tanggal</th></tr></thead>
            <tbody>
                @foreach($receipts as $r)
                <tr>
                    <td><a href="{{ route('goods-receipts.show', $r) }}">{{ $r->receipt_number }}</a></td>
                    <td>{{ $r->supplier->name ?? '-' }}</td>
                    <td>{{ $r->warehouse->name ?? '-' }}</td>
                    <td class="small">{{ $r->receipt_date->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$receipts" />
        @endif
    </x-card>
</x-app-layout>
