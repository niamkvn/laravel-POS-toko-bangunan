<x-app-layout title="Laporan Stok">
    <div class="mb-3"><x-stat-card label="Total Nilai Inventory" value="Rp {{ number_format($inventoryValue,0,',','.') }}" variant="primary" /></div>
    <x-card title="Laporan Stok">
        <x-table>
            <thead><tr><th>Produk</th><th>Kategori</th><th>Stok</th><th>Avg Cost</th><th>Nilai Stok</th></tr></thead>
            <tbody>
                @foreach($products as $p)
                <tr>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->category->name ?? '-' }}</td>
                    <td>{{ $p->totalStock() }}</td>
                    <td>Rp {{ number_format($p->average_cost,0,',','.') }}</td>
                    <td class="fw-bold">Rp {{ number_format($p->totalStock()*$p->average_cost,0,',','.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
    </x-card>
</x-app-layout>
