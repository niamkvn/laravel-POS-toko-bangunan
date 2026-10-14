<x-app-layout :title="$product->name">
    <div class="row g-3 mb-3">
        <div class="col-md-3"><x-stat-card label="Total Stok" value="{{ rtrim(rtrim(number_format($product->totalStock(),2,'.',''),'0'),'.') }}" variant="primary" /></div>
        <div class="col-md-3"><x-stat-card label="Harga Beli" value="Rp {{ number_format($product->purchase_price,0,',','.') }}" variant="soft" /></div>
        <div class="col-md-3"><x-stat-card label="Harga Jual" value="Rp {{ number_format($product->default_sell_price,0,',','.') }}" variant="mint" /></div>
        <div class="col-md-3"><x-stat-card label="Avg Cost (HPP)" value="Rp {{ number_format($product->average_cost,0,',','.') }}" variant="warning" /></div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <x-card :title="$product->name" :subtitle="$product->sku . ' — ' . $product->product_code">
                <p class="mb-1"><strong>Kategori:</strong> {{ $product->category->name ?? '-' }}</p>
                <p class="mb-1"><strong>Brand:</strong> {{ $product->brand->name ?? '-' }}</p>
                <p class="mb-1"><strong>Satuan Dasar:</strong> {{ $product->baseUnit->name ?? '-' }}</p>
                <p class="mb-1"><strong>Barcode:</strong> {{ $product->barcode ?? '-' }}</p>
                <p class="mb-0"><strong>Min/Max Stok:</strong> {{ $product->minimum_stock }} / {{ $product->maximum_stock ?? '-' }}</p>
            </x-card>

            <x-card title="Satuan &amp; Konversi" class="mt-3">
                <x-table>
                    <thead><tr><th>Satuan</th><th>Konversi</th><th>Beli</th><th>Jual</th></tr></thead>
                    <tbody>
                        @foreach($product->units as $u)
                        <tr>
                            <td>{{ $u->unit->name ?? '-' }}</td>
                            <td>1 = {{ $u->conversion_factor }} {{ $product->baseUnit->code ?? '' }}</td>
                            <td>{{ $u->is_purchase_unit ? 'Ya' : '-' }}</td>
                            <td>{{ $u->is_sale_unit ? 'Ya' : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </x-table>
            </x-card>
        </div>

        <div class="col-lg-6">
            <x-card title="Stok per Gudang">
                @if($product->stocks->isEmpty())
                    <p class="text-muted small mb-0">Belum ada stok.</p>
                @else
                <x-table>
                    <thead><tr><th>Gudang</th><th>Qty</th></tr></thead>
                    <tbody>
                        @foreach($product->stocks as $s)
                        <tr><td>{{ $s->warehouse->name ?? '-' }}</td><td>{{ $s->quantity }}</td></tr>
                        @endforeach
                    </tbody>
                </x-table>
                @endif
            </x-card>

            <x-card title="Daftar Harga" class="mt-3">
                @foreach($product->prices as $p)
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="small">{{ $p->priceLevel->name ?? 'Umum' }} (min {{ $p->min_qty }})</span>
                    <span class="fw-bold small">Rp {{ number_format($p->price,0,',','.') }}</span>
                </div>
                @endforeach
                <a href="{{ route('products.prices', $product) }}" class="btn btn-sm btn-outline btn-nb mt-3">Kelola Harga</a>
            </x-card>
        </div>
    </div>
</x-app-layout>
