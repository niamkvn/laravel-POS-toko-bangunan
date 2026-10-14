<x-app-layout title="Harga Produk">
    <x-card :title="'Harga — ' . $product->name" subtitle="Kelola harga berdasarkan price level dan quantity tier">
        <form method="POST" action="{{ route('products.prices.store', $product) }}" class="row g-2 mb-4 align-items-end">
            @csrf
            <div class="col-md-3">
                <x-select label="Price Level" name="price_level_id" :options="$priceLevels" placeholder="Umum / Default" />
            </div>
            <div class="col-md-3"><x-input type="number" step="0.001" label="Min Qty" name="min_qty" value="1" required /></div>
            <div class="col-md-3"><x-input type="number" step="0.01" label="Harga" name="price" required /></div>
            <div class="col-md-3"><button class="btn btn-primary btn-nb w-100">Tambah Harga</button></div>
        </form>

        @if($product->prices->isEmpty())
            <x-empty-state title="Belum ada harga khusus" description="Produk menggunakan harga jual default." />
        @else
        <x-table>
            <thead><tr><th>Price Level</th><th>Min Qty</th><th>Harga</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @foreach($product->prices as $price)
                <tr>
                    <td>{{ $price->priceLevel->name ?? 'Umum (Default)' }}</td>
                    <td>{{ $price->min_qty }}</td>
                    <td>Rp {{ number_format($price->price, 0, ',', '.') }}</td>
                    <td class="text-end">
                        <form action="{{ route('products.prices.destroy', $price) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus harga ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger btn-nb"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        @endif

        <a href="{{ route('products.index') }}" class="btn btn-outline btn-nb mt-3">Kembali</a>
    </x-card>
</x-app-layout>
