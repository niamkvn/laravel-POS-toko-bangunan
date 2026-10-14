<x-app-layout title="Produk">
    <x-card>
        <x-slot:actions>
            @can('product.create')<a href="{{ route('products.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Tambah Produk</a>@endcan
        </x-slot:actions>

        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4"><x-search name="search" :value="request('search')" placeholder="Cari nama, SKU, barcode..." /></div>
            <div class="col-md-3">
                <x-select name="category_id" :options="$categories" :selected="request('category_id')" placeholder="Semua Kategori" auto-submit />
            </div>
            <div class="col-md-2"><button class="btn btn-outline btn-nb w-100">Filter</button></div>
        </form>

        @if($products->isEmpty())
            <x-empty-state title="Belum ada produk" description="Tambahkan produk pertama Anda." action-label="Tambah Produk" :action-href="route('products.create')" />
        @else
        <x-table>
            <thead><tr><th>Produk</th><th>Kategori</th><th>Satuan</th><th>Harga Jual</th><th>Stok</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @foreach($products as $p)
                <tr>
                    <td>
                        <div class="fw-bold">{{ $p->name }}</div>
                        <div class="text-muted small">{{ $p->sku }}</div>
                    </td>
                    <td>{{ $p->category->name ?? '-' }}</td>
                    <td>{{ $p->baseUnit->code ?? '-' }}</td>
                    <td>Rp {{ number_format($p->default_sell_price, 0, ',', '.') }}</td>
                    <td>
                        @php $stock = $p->totalStock(); @endphp
                        <x-badge :variant="$stock <= $p->minimum_stock ? 'danger' : 'mint'">{{ rtrim(rtrim(number_format($stock,2,'.',''), '0'), '.') }}</x-badge>
                    </td>
                    <td><x-badge :variant="$p->active ? 'mint' : 'danger'">{{ $p->active ? 'Aktif' : 'Nonaktif' }}</x-badge></td>
                    <td class="text-end">
                        <a href="{{ route('products.prices', $p) }}" class="btn btn-sm btn-outline btn-nb" title="Harga"><i class="bi bi-tags"></i></a>
                        @can('product.edit')<a href="{{ route('products.edit', $p) }}" class="btn btn-sm btn-outline btn-nb"><i class="bi bi-pencil"></i></a>@endcan
                        @can('product.delete')
                        <form action="{{ route('products.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus produk ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger btn-nb"><i class="bi bi-trash"></i></button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$products" />
        @endif
    </x-card>
</x-app-layout>
