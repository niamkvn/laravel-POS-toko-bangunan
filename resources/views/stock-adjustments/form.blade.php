<x-app-layout title="Buat Stock Adjustment">
    <form method="POST" action="{{ route('stock-adjustments.store') }}" x-data="{
        rows: [{product_id:'',adjustment_qty:0,notes:''}],
        products: {{ Js::from($products) }},
    }">
        @csrf
        <x-card title="Buat Stock Adjustment">
            <div class="row g-3 mb-3">
                <div class="col-md-6"><x-select label="Gudang" name="warehouse_id" :options="$warehouses" required /></div>
            </div>
            <div class="mb-3"><x-input label="Alasan Adjustment (Wajib)" name="reason" required /></div>

            <table class="table">
                <thead><tr><th>Produk</th><th>Qty Adjustment (+/-)</th><th>Catatan</th><th></th></tr></thead>
                <tbody>
                    <template x-for="(row, i) in rows" :key="i">
                        <tr>
                            <td>
                                <select :name="`items[${i}][product_id]`" class="form-select form-select-sm" x-model.number="row.product_id" required>
                                    <option value="">-- Pilih --</option>
                                    <template x-for="(name, id) in products" :key="id"><option :value="id" x-text="name"></option></template>
                                </select>
                            </td>
                            <td><input type="number" step="0.001" :name="`items[${i}][adjustment_qty]`" class="form-control form-control-sm" x-model.number="row.adjustment_qty" placeholder="+10 atau -5"></td>
                            <td><input type="text" :name="`items[${i}][notes]`" class="form-control form-control-sm" x-model="row.notes"></td>
                            <td><button type="button" class="btn btn-sm btn-danger btn-nb" @click="rows.splice(i,1)"><i class="bi bi-trash"></i></button></td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <button type="button" class="btn btn-sm btn-outline btn-nb" @click="rows.push({product_id:'',adjustment_qty:0,notes:''})"><i class="bi bi-plus"></i> Tambah Baris</button>

            <p class="small text-muted mt-3">Adjustment dengan total qty di atas threshold akan membutuhkan approval Manager sebelum stok diperbarui.</p>
            <button type="submit" class="btn btn-primary btn-nb">Simpan Adjustment</button>
            <a href="{{ route('stock-adjustments.index') }}" class="btn btn-outline btn-nb">Batal</a>
        </x-card>
    </form>
</x-app-layout>
