<x-app-layout title="Buat Delivery Order">
    <x-card title="Cari Invoice">
        <form method="GET" class="row g-2">
            <div class="col-md-6"><input type="text" name="invoice" class="form-control" placeholder="Masukkan No. Invoice..." value="{{ request('invoice') }}"></div>
            <div class="col-md-3"><button class="btn btn-outline btn-nb">Cari</button></div>
        </form>
    </x-card>

    @if($sale)
    <x-card :title="'Buat DO untuk ' . $sale->invoice_number" class="mt-3">
        <form method="POST" action="{{ route('delivery-orders.store') }}" x-data="{
            items: {{ Js::from($sale->items->values()->map(fn($i)=>['sale_item_id'=>$i->id,'name'=>$i->product->name,'unit'=>$i->unit->code,'max'=>(float)$i->qty,'qty'=>(float)$i->qty])) }}
        }">
            @csrf
            <input type="hidden" name="sale_id" value="{{ $sale->id }}">
            <div class="row g-3 mb-3">
                <div class="col-md-4"><x-select label="Driver" name="driver_id" :options="$drivers" /></div>
                <div class="col-md-4"><x-select label="Kendaraan" name="vehicle_id" :options="$vehicles" /></div>
                <div class="col-md-4"><x-input type="date" label="Tanggal Kirim" name="delivery_date" :value="now()->format('Y-m-d')" required /></div>
                <div class="col-md-12"><x-input label="Alamat Pengiriman" name="delivery_address" :value="$sale->customer->address ?? ''" /></div>
            </div>

            <table class="table">
                <thead><tr><th>Produk</th><th>Qty Tersedia</th><th>Qty Kirim</th></tr></thead>
                <tbody>
                    <template x-for="(item, i) in items" :key="item.sale_item_id">
                        <tr>
                            <td x-text="item.name"></td>
                            <td x-text="item.max + ' ' + item.unit"></td>
                            <td>
                                <input type="hidden" :name="`items[${i}][sale_item_id]`" :value="item.sale_item_id">
                                <input type="number" step="0.001" :max="item.max" :name="`items[${i}][qty]`" class="form-control form-control-sm" x-model.number="item.qty">
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <div class="mb-3"><x-input label="Catatan" name="notes" /></div>
            <button type="submit" class="btn btn-primary btn-nb">Buat Delivery Order</button>
        </form>
    </x-card>
    @endif
</x-app-layout>
