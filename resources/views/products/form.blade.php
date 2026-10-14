<x-app-layout :title="$product->exists ? 'Edit Produk' : 'Tambah Produk'">
    <form method="POST" action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}">
        @csrf
        @if($product->exists) @method('PUT') @endif

        <div class="row g-3">
            <div class="col-lg-8">
                <x-card title="Informasi Produk">
                    <div class="row g-3">
                        <div class="col-md-12"><x-input label="Nama Produk" name="name" :value="$product->name" required /></div>
                        <div class="col-md-6"><x-input label="SKU" name="sku" :value="$product->sku" required /></div>
                        <div class="col-md-6"><x-input label="Barcode" name="barcode" :value="$product->barcode" /></div>
                        <div class="col-md-6"><x-select label="Kategori" name="category_id" :options="$categories" :selected="$product->category_id" /></div>
                        <div class="col-md-6"><x-select label="Brand" name="brand_id" :options="$brands" :selected="$product->brand_id" /></div>
                        <div class="col-md-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>
                </x-card>

                <x-card title="Harga & Pajak" class="mt-3">
                    <div class="row g-3">
                        <div class="col-md-4"><x-input type="number" step="0.01" label="Harga Beli" name="purchase_price" :value="$product->purchase_price" required /></div>
                        <div class="col-md-4"><x-input type="number" step="0.01" label="Harga Jual Default" name="default_sell_price" :value="$product->default_sell_price" required /></div>
                        <div class="col-md-4"><x-select label="Pajak" name="tax_id" :options="$taxes" :selected="$product->tax_id" placeholder="Tanpa Pajak" /></div>
                    </div>
                </x-card>

                <x-card title="Satuan &amp; Konversi" class="mt-3" x-data="{
                    baseUnit: '{{ old('unit_id', $product->unit_id) }}',
                    rows: {{ Js::from($product->exists ? $product->units->where('is_base', false)->values()->map(fn($u) => ['unit_id' => $u->unit_id, 'conversion_factor' => (float) $u->conversion_factor, 'is_purchase_unit' => $u->is_purchase_unit, 'is_sale_unit' => $u->is_sale_unit]) : []) }}
                }">
                    <label class="form-label">Satuan Dasar (Base Unit)</label>
                    <select name="unit_id" class="form-select mb-3" x-model="baseUnit" required>
                        <option value="">-- Pilih Satuan --</option>
                        @foreach($units as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>

                    <p class="small text-muted">Satuan tambahan (misal: 1 Dus = 10 Pcs). Stok selalu dihitung dalam satuan dasar.</p>

                    <template x-for="(row, i) in rows" :key="i">
                        <div class="row g-2 align-items-end mb-2 p-2" style="border:1px solid var(--color-gray);border-radius:6px;">
                            <div class="col-md-4">
                                <label class="form-label small">Satuan</label>
                                <select :name="`extra_units[${i}][unit_id]`" class="form-select form-select-sm" x-model="row.unit_id">
                                    <option value="">-- Pilih --</option>
                                    @foreach($units as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">1 Satuan = ? Base Unit</label>
                                <input type="number" step="0.0001" :name="`extra_units[${i}][conversion_factor]`" class="form-control form-control-sm" x-model="row.conversion_factor">
                            </div>
                            <div class="col-md-2">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" :name="`extra_units[${i}][is_purchase_unit]`" value="1" x-model="row.is_purchase_unit">
                                    <label class="form-check-label small">Beli</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" :name="`extra_units[${i}][is_sale_unit]`" value="1" x-model="row.is_sale_unit">
                                    <label class="form-check-label small">Jual</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-sm btn-danger btn-nb" @click="rows.splice(i,1)"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    </template>

                    <button type="button" class="btn btn-sm btn-outline btn-nb mt-2" @click="rows.push({unit_id:'',conversion_factor:1,is_purchase_unit:false,is_sale_unit:true})">
                        <i class="bi bi-plus-lg"></i> Tambah Satuan
                    </button>
                </x-card>
            </div>

            <div class="col-lg-4">
                <x-card title="Stok &amp; Status">
                    <div class="row g-3">
                        <div class="col-md-6"><x-input type="number" step="0.001" label="Stok Minimum" name="minimum_stock" :value="$product->minimum_stock ?? 0" required /></div>
                        <div class="col-md-6"><x-input type="number" step="0.001" label="Stok Maksimum" name="maximum_stock" :value="$product->maximum_stock" /></div>
                    </div>
                    <div class="form-check mt-3">
                        <input type="checkbox" name="allow_negative_stock" value="1" class="form-check-input" id="allow_negative_stock" @checked($product->allow_negative_stock)>
                        <label class="form-check-label" for="allow_negative_stock">Izinkan stok minus</label>
                    </div>
                    <div class="form-check mt-2">
                        <input type="checkbox" name="active" value="1" class="form-check-input" id="active" @checked($product->exists ? $product->active : true)>
                        <label class="form-check-label" for="active">Aktif</label>
                    </div>
                </x-card>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary btn-nb w-100">Simpan Produk</button>
                    <a href="{{ route('products.index') }}" class="btn btn-outline btn-nb">Batal</a>
                </div>
            </div>
        </div>
    </form>
</x-app-layout>
