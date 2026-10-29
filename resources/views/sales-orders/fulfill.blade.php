<x-app-layout title="Fulfill Sales Order">
    <form method="POST" action="{{ route('sales-orders.fulfill', $salesOrder) }}" x-data="{
        rows: {{ Js::from($salesOrder->items->map(fn($i)=>['id'=>$i->id,'name'=>$i->product->name,'unit'=>$i->unit->code,'remaining'=>$i->remainingQty(),'qty'=>$i->remainingQty(),'price'=>(float)$i->price])) }},
        payments: [{payment_method_id: {{ \App\Models\PaymentMethod::first()->id ?? 1 }}, amount: 0}],
        total() { return this.rows.reduce((s,r)=> s + (r.qty*r.price), 0); },
        totalPaid() { return this.payments.reduce((s,p)=> s + (Number(p.amount)||0), 0); },
    }" x-init="$watch('rows', () => { payments[0].amount = total(); }, {deep:true}); payments[0].amount = total();">
        @csrf
        <x-card :title="'Fulfill — ' . $salesOrder->number">
            <div class="mb-3"><x-select label="Gudang Pengiriman" name="warehouse_id" :options="$warehouses" required /></div>

            <table class="table">
                <thead><tr><th>Produk</th><th>Sisa Order</th><th>Qty Dikirim</th><th>Harga</th><th>Subtotal</th></tr></thead>
                <tbody>
                    <template x-for="(row, i) in rows" :key="row.id">
                        <tr>
                            <td x-text="row.name"></td>
                            <td x-text="row.remaining + ' ' + row.unit"></td>
                            <td>
                                <input type="hidden" :name="`items[${i}][sales_order_item_id]`" :value="row.id">
                                <input type="number" step="0.001" :max="row.remaining" :name="`items[${i}][qty]`" class="form-control form-control-sm" x-model.number="row.qty">
                            </td>
                            <td x-text="new Intl.NumberFormat('id-ID').format(row.price)"></td>
                            <td x-text="new Intl.NumberFormat('id-ID').format(row.qty*row.price)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <div class="text-end mb-3"><h5>Total: Rp <span x-text="new Intl.NumberFormat('id-ID').format(total())"></span></h5></div>

            <h6>Pembayaran</h6>
            <template x-for="(p, idx) in payments" :key="idx">
                <div class="d-flex gap-2 mb-2">
                    <select :name="`payments[${idx}][payment_method_id]`" class="form-select" x-model.number="p.payment_method_id">
                        @foreach(\App\Models\PaymentMethod::where('active',true)->get() as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" step="1" :name="`payments[${idx}][amount]`" class="form-control" x-model.number="p.amount">
                </div>
            </template>
            <p class="small text-muted">Jika pembayaran kurang dari total, sisanya akan menjadi piutang (jika kredit sale).</p>

            <button type="submit" class="btn btn-primary btn-nb">Proses Fulfillment</button>
            <a href="{{ route('sales-orders.show', $salesOrder) }}" class="btn btn-outline btn-nb">Batal</a>
        </x-card>
    </form>
</x-app-layout>
