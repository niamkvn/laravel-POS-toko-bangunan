<x-app-layout title="Buat Retur Penjualan">
    <x-card title="Cari Invoice">
        <form method="GET" class="row g-2">
            <div class="col-md-6"><input type="text" name="invoice" class="form-control" placeholder="Masukkan No. Invoice..." id="invoiceSearch"></div>
            <div class="col-md-3"><button type="button" class="btn btn-outline btn-nb" onclick="searchInvoice()">Cari</button></div>
        </form>
    </x-card>

    @if($sale)
    <x-card :title="'Retur untuk Invoice ' . $sale->invoice_number" class="mt-3">
        <form method="POST" action="{{ route('sale-returns.store') }}" x-data="{
            items: {{ Js::from($sale->items->filter(fn($i)=>$i->returnableQty() > 0)->values()->map(fn($i)=>['sale_item_id'=>$i->id,'name'=>$i->product->name,'unit'=>$i->unit->code,'returnable'=>$i->returnableQty(),'qty'=>0,'price'=>(float)$i->price])) }}
        }">
            @csrf
            <input type="hidden" name="sale_id" value="{{ $sale->id }}">
            <table class="table">
                <thead><tr><th>Produk</th><th>Qty Dapat Diretur</th><th>Qty Retur</th><th>Harga</th></tr></thead>
                <tbody>
                    <template x-for="(item, i) in items" :key="item.sale_item_id">
                        <tr>
                            <td x-text="item.name"></td>
                            <td x-text="item.returnable + ' ' + item.unit"></td>
                            <td>
                                <input type="hidden" :name="`items[${i}][sale_item_id]`" :value="item.sale_item_id">
                                <input type="number" step="0.001" :max="item.returnable" min="0" :name="`items[${i}][qty]`" class="form-control form-control-sm" x-model.number="item.qty">
                            </td>
                            <td x-text="new Intl.NumberFormat('id-ID').format(item.price)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <div class="mb-3"><x-input label="Alasan Retur" name="reason" /></div>
            <button type="submit" class="btn btn-primary btn-nb">Proses Retur</button>
        </form>
    </x-card>
    @endif

    <script>
        function searchInvoice() {
            const v = document.getElementById('invoiceSearch').value;
            window.location.href = `{{ route('sale-returns.create') }}?invoice=${encodeURIComponent(v)}`;
        }
    </script>
</x-app-layout>
