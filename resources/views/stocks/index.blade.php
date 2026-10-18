<x-app-layout title="Stok">
    <x-card title="Stok Produk">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4"><x-search name="search" :value="request('search')" placeholder="Cari produk..." /></div>
            <div class="col-md-3">
                <x-select name="warehouse_id" :options="$warehouses" :selected="request('warehouse_id')" placeholder="Semua Gudang" auto-submit />
            </div>
            <div class="col-md-3 d-flex align-items-center gap-2">
                <input type="checkbox" name="low_stock" value="1" class="form-check-input" id="low_stock" onchange="this.form.submit()" @checked(request('low_stock'))>
                <label class="form-check-label small" for="low_stock">Hanya stok kritis</label>
            </div>
        </form>

        @if($products->isEmpty())
            <x-empty-state title="Tidak ada data stok" />
        @else
        <x-table>
            <thead><tr><th>Produk</th><th>Kategori</th><th>Satuan</th><th>Per Gudang</th><th>Total</th><th>Min</th></tr></thead>
            <tbody>
                @foreach($products as $p)
                <tr class="{{ $p->isLowStock() ? 'diff-negative' : '' }}">
                    <td class="fw-bold">{{ $p->name }} <div class="text-muted small fw-normal">{{ $p->sku }}</div></td>
                    <td>{{ $p->category->name ?? '-' }}</td>
                    <td>{{ $p->baseUnit->code ?? '-' }}</td>
                    <td class="small">
                        @foreach($p->stocks as $s)
                            {{ $s->warehouse->name ?? '-' }}: <strong>{{ rtrim(rtrim(number_format($s->quantity,2,'.',''),'0'),'.') }}</strong>@if(!$loop->last), @endif
                        @endforeach
                    </td>
                    <td class="fw-bold">{{ rtrim(rtrim(number_format($p->totalStock(),2,'.',''),'0'),'.') }}</td>
                    <td>{{ $p->minimum_stock }}</td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$products" />
        @endif
    </x-card>
</x-app-layout>
