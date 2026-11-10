<x-app-layout title="Buat Stock Transfer">
    <form method="POST" action="{{ route('stock-transfers.store') }}" x-data="{
        rows: [{product_id:'',unit_id:'',qty:1}],
        products: {{ Js::from($products->map(fn($p)=>['id'=>$p->id,'name'=>$p->name,'units'=>$p->units->map(fn($u)=>['unit_id'=>$u->unit_id,'code'=>$u->unit->code])])) }},
        unitsFor(pid) { const p = this.products.find(p=>p.id===Number(pid)); return p ? p.units : []; },
        onProductChange(row) { const units = this.unitsFor(row.product_id); if (units.length) row.unit_id = units[0].unit_id; },
    }">
        @csrf
        <x-card title="Buat Stock Transfer">
            <div class="row g-3 mb-3">
                <div class="col-md-5"><x-select label="Gudang Asal" name="source_warehouse_id" :options="$warehouses" required /></div>
                <div class="col-md-5"><x-select label="Gudang Tujuan" name="destination_warehouse_id" :options="$warehouses" required /></div>
                <div class="col-md-2"><x-input type="date" label="Tanggal" name="transfer_date" :value="now()->format('Y-m-d')" required /></div>
            </div>

            <table class="table">
                <thead><tr><th>Produk</th><th>Satuan</th><th>Qty</th><th></th></tr></thead>
                <tbody>
                    <template x-for="(row, i) in rows" :key="i">
                        <tr>
                            <td>
                                <select :name="`items[${i}][product_id]`" class="form-select form-select-sm" x-model.number="row.product_id" @change="onProductChange(row)" required>
                                    <option value="">-- Pilih --</option>
                                    <template x-for="p in products" :key="p.id"><option :value="p.id" x-text="p.name"></option></template>
                                </select>
                            </td>
                            <td>
                                <select :name="`items[${i}][unit_id]`" class="form-select form-select-sm" x-model.number="row.unit_id">
                                    <template x-for="u in unitsFor(row.product_id)" :key="u.unit_id"><option :value="u.unit_id" x-text="u.code"></option></template>
                                </select>
                            </td>
                            <td><input type="number" step="0.001" :name="`items[${i}][qty]`" class="form-control form-control-sm" x-model.number="row.qty"></td>
                            <td><button type="button" class="btn btn-sm btn-danger btn-nb" @click="rows.splice(i,1)"><i class="bi bi-trash"></i></button></td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <button type="button" class="btn btn-sm btn-outline btn-nb" @click="rows.push({product_id:'',unit_id:'',qty:1})"><i class="bi bi-plus"></i> Tambah Baris</button>

            <div class="mb-3 mt-3"><x-input label="Catatan" name="notes" /></div>
            <button type="submit" class="btn btn-primary btn-nb">Simpan Transfer</button>
            <a href="{{ route('stock-transfers.index') }}" class="btn btn-outline btn-nb">Batal</a>
        </x-card>
    </form>
</x-app-layout>
