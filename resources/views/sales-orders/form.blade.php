<x-app-layout title="Buat Sales Order">
    <form method="POST" action="{{ route('sales-orders.store') }}" x-data="{
        rows: [{product_id:'',unit_id:'',qty:1,price:0}],
        products: {{ Js::from($products->map(fn($p)=>['id'=>$p->id,'name'=>$p->name,'units'=>$p->units->map(fn($u)=>['unit_id'=>$u->unit_id,'code'=>$u->unit->code,'price'=>(float)$p->default_sell_price*(float)$u->conversion_factor])])) }},
        unitsFor(pid) { const p = this.products.find(p=>p.id===Number(pid)); return p ? p.units : []; },
        onProductChange(row) { const units = this.unitsFor(row.product_id); if (units.length) { row.unit_id = units[0].unit_id; row.price = units[0].price; } },
        total() { return this.rows.reduce((s,r)=> s + (r.qty*r.price), 0); }
    }">
        @csrf
        <x-card title="Buat Sales Order">
            <div class="row g-3 mb-3">
                <div class="col-md-4"><x-select label="Customer" name="customer_id" :options="$customers" required /></div>
                <div class="col-md-4"><x-select label="Project (opsional)" name="customer_project_id" :options="$projects" /></div>
                <div class="col-md-4"><x-input type="date" label="Tanggal" name="date" :value="now()->format('Y-m-d')" required /></div>
            </div>

            <table class="table">
                <thead><tr><th>Produk</th><th>Satuan</th><th>Qty</th><th>Harga</th><th>Subtotal</th><th></th></tr></thead>
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
                            <td><input type="number" step="1" :name="`items[${i}][price]`" class="form-control form-control-sm" x-model.number="row.price"></td>
                            <td class="small" x-text="new Intl.NumberFormat('id-ID').format(row.qty*row.price)"></td>
                            <td><button type="button" class="btn btn-sm btn-danger btn-nb" @click="rows.splice(i,1)"><i class="bi bi-trash"></i></button></td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <button type="button" class="btn btn-sm btn-outline btn-nb" @click="rows.push({product_id:'',unit_id:'',qty:1,price:0})"><i class="bi bi-plus"></i> Tambah Baris</button>

            <div class="text-end mt-3"><h5>Total: Rp <span x-text="new Intl.NumberFormat('id-ID').format(total())"></span></h5></div>

            <div class="mb-3"><x-input label="Catatan" name="notes" /></div>
            <button type="submit" class="btn btn-primary btn-nb">Simpan Sales Order</button>
            <a href="{{ route('sales-orders.index') }}" class="btn btn-outline btn-nb">Batal</a>
        </x-card>
    </form>
</x-app-layout>
