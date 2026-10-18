<x-app-layout title="Stock Card">
    <x-card title="Stock Card">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4"><x-select label="Produk" name="product_id" :options="$products" :selected="request('product_id')" placeholder="Pilih Produk" /></div>
            <div class="col-md-3">
                <x-select label="Gudang" name="warehouse_id" :options="$warehouses" :selected="request('warehouse_id')" placeholder="Semua Gudang" />
            </div>
            <div class="col-md-3"><x-date-filter :from="request('date_from')" :to="request('date_to')" /></div>
            <div class="col-md-2"><x-field-button><button class="btn btn-primary btn-nb w-100">Tampilkan</button></x-field-button></div>
        </form>

        @if(!request('product_id'))
            <x-empty-state title="Pilih produk" description="Pilih produk untuk melihat riwayat pergerakan stok." />
        @elseif($movements->isEmpty())
            <x-empty-state title="Tidak ada pergerakan stok" />
        @else
        <x-table>
            <thead><tr><th>Tanggal</th><th>Gudang</th><th>Tipe</th><th>Qty</th><th>Saldo Awal</th><th>Saldo Akhir</th><th>HPP</th></tr></thead>
            <tbody>
                @foreach($movements as $m)
                <tr>
                    <td class="small">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $m->warehouse->name ?? '-' }}</td>
                    <td><x-badge :variant="$m->quantity >= 0 ? 'mint' : 'danger'">{{ $m->movement_type }}</x-badge></td>
                    <td class="fw-bold">{{ $m->quantity >= 0 ? '+' : '' }}{{ $m->quantity }}</td>
                    <td>{{ $m->balance_before }}</td>
                    <td>{{ $m->balance_after }}</td>
                    <td>Rp {{ number_format($m->unit_cost,0,',','.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$movements" />
        @endif
    </x-card>
</x-app-layout>
