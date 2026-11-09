<x-app-layout title="Buat Retur Pembelian">
    <x-card title="Cari No. Pembelian">
        <form method="GET" class="row g-2">
            <div class="col-md-6"><input type="text" name="purchase_number" class="form-control" placeholder="Masukkan No. Pembelian (contoh: PB-20260910-0001)" value="{{ request('purchase_number') }}"></div>
            <div class="col-md-3"><button class="btn btn-outline btn-nb">Cari</button></div>
        </form>
    </x-card>

    @if($purchase)
    <x-card :title="'Retur untuk ' . $purchase->purchase_number" class="mt-3">
        <form method="POST" action="{{ route('purchase-returns.store') }}" x-data="{
            items: {{ Js::from($purchase->items->filter(fn($i)=>$i->returnableQty() > 0)->values()->map(fn($i)=>['purchase_item_id'=>$i->id,'name'=>$i->product->name,'unit'=>$i->unit->code,'returnable'=>$i->returnableQty(),'qty'=>0,'unit_cost'=>(float)$i->unit_cost])) }}
        }">
            @csrf
            <input type="hidden" name="purchase_id" value="{{ $purchase->id }}">
            <table class="table">
                <thead><tr><th>Produk</th><th>Qty Dapat Diretur</th><th>Qty Retur</th><th>Harga</th></tr></thead>
                <tbody>
                    <template x-for="(item, i) in items" :key="item.purchase_item_id">
                        <tr>
                            <td x-text="item.name"></td>
                            <td x-text="item.returnable + ' ' + item.unit"></td>
                            <td>
                                <input type="hidden" :name="`items[${i}][purchase_item_id]`" :value="item.purchase_item_id">
                                <input type="number" step="0.001" :max="item.returnable" min="0" :name="`items[${i}][qty]`" class="form-control form-control-sm" x-model.number="item.qty">
                            </td>
                            <td x-text="new Intl.NumberFormat('id-ID').format(item.unit_cost)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <div class="mb-3"><x-input label="Alasan Retur" name="reason" /></div>
            <button type="submit" class="btn btn-primary btn-nb">Proses Retur</button>
        </form>
    </x-card>
    @endif
</x-app-layout>
