<x-app-layout title="Terima Barang">
    <form method="POST" action="{{ route('goods-receipts.store') }}" x-data="{
        rows: {{ Js::from($po ? $po->items->map(fn($i)=>['product_id'=>$i->product_id,'unit_id'=>$i->unit_id,'qty'=>(float)$i->qty-(float)$i->received_qty,'unit_cost'=>(float)$i->product->purchase_price,'damaged_qty'=>0,'purchase_order_item_id'=>$i->id]) : [['product_id'=>'','unit_id'=>'','qty'=>1,'unit_cost'=>0,'damaged_qty'=>0,'purchase_order_item_id'=>null]]) }},
        products: {{ Js::from($products->map(fn($p)=>['id'=>$p->id,'name'=>$p->name,'units'=>$p->units->map(fn($u)=>['unit_id'=>$u->unit_id,'code'=>$u->unit->code])])) }},
        unitsFor(pid) { const p = this.products.find(p=>p.id===Number(pid)); return p ? p.units : []; },
    }">
        @csrf
        @if($po)<input type="hidden" name="purchase_order_id" value="{{ $po->id }}">@endif
        <x-card title="Terima Barang / Goods Receipt">
            <div class="row g-3 mb-3">
                <div class="col-md-6"><x-select label="Supplier" name="supplier_id" :options="$suppliers" :selected="$po->supplier_id ?? null" required /></div>
                <div class="col-md-6"><x-select label="Gudang Penerima" name="warehouse_id" :options="$warehouses" :selected="$po->warehouse_id ?? null" required /></div>
            </div>

            <table class="table">
                <thead><tr><th>Produk</th><th>Satuan</th><th>Qty Diterima</th><th>Qty Rusak</th><th>Harga Beli/Unit</th><th></th></tr></thead>
                <tbody>
                    <template x-for="(row, i) in rows" :key="i">
                        <tr>
                            <td>
                                <select :name="`items[${i}][product_id]`" class="form-select form-select-sm" x-model.number="row.product_id" :disabled="!!row.purchase_order_item_id" required>
                                    <option value="">-- Pilih --</option>
                                    <template x-for="p in products" :key="p.id"><option :value="p.id" x-text="p.name"></option></template>
                                </select>
                                <input type="hidden" :name="`items[${i}][purchase_order_item_id]`" :value="row.purchase_order_item_id">
                            </td>
                            <td>
                                <select :name="`items[${i}][unit_id]`" class="form-select form-select-sm" x-model.number="row.unit_id">
                                    <template x-for="u in unitsFor(row.product_id)" :key="u.unit_id"><option :value="u.unit_id" x-text="u.code"></option></template>
                                </select>
                            </td>
                            <td><input type="number" step="0.001" :name="`items[${i}][qty]`" class="form-control form-control-sm" x-model.number="row.qty"></td>
                            <td><input type="number" step="0.001" :name="`items[${i}][damaged_qty]`" class="form-control form-control-sm" x-model.number="row.damaged_qty"></td>
                            <td><input type="number" step="1" :name="`items[${i}][unit_cost]`" class="form-control form-control-sm" x-model.number="row.unit_cost"></td>
                            <td><button type="button" class="btn btn-sm btn-danger btn-nb" @click="rows.splice(i,1)" x-show="!row.purchase_order_item_id"><i class="bi bi-trash"></i></button></td>
                        </tr>
                    </template>
                </tbody>
            </table>
            @unless($po)
            <button type="button" class="btn btn-sm btn-outline btn-nb" @click="rows.push({product_id:'',unit_id:'',qty:1,unit_cost:0,damaged_qty:0,purchase_order_item_id:null})"><i class="bi bi-plus"></i> Tambah Baris</button>
            @endunless

            <div class="mb-3 mt-3"><x-input label="Catatan" name="notes" /></div>
            <button type="submit" class="btn btn-primary btn-nb">Posting Penerimaan &amp; Update Stok</button>
            <a href="{{ route('goods-receipts.index') }}" class="btn btn-outline btn-nb">Batal</a>
        </x-card>
    </form>
</x-app-layout>
