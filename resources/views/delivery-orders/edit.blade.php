<x-app-layout title="Update Delivery Order">
    <x-card :title="'Update — ' . $deliveryOrder->do_number" style="max-width:560px;">
        <form method="POST" action="{{ route('delivery-orders.update', $deliveryOrder) }}">
            @csrf @method('PUT')
            <div class="mb-3"><x-select label="Driver" name="driver_id" :options="$drivers" :selected="$deliveryOrder->driver_id" /></div>
            <div class="mb-3"><x-select label="Kendaraan" name="vehicle_id" :options="$vehicles" :selected="$deliveryOrder->vehicle_id" /></div>
            <div class="mb-3"><x-input type="date" label="Tanggal Kirim" name="delivery_date" :value="$deliveryOrder->delivery_date->format('Y-m-d')" required /></div>
            <div class="mb-3">
                <x-select label="Status" name="status" :selected="$deliveryOrder->status" required
                    :options="['pending'=>'Pending','preparing'=>'Preparing','shipped'=>'Shipped','delivered'=>'Delivered','cancelled'=>'Cancelled']" />
            </div>
            <div class="mb-3"><x-input label="Bukti Pengiriman (No. Resi / Nama Penerima)" name="proof_of_delivery" :value="$deliveryOrder->proof_of_delivery" /></div>
            <div class="mb-3"><x-input label="Catatan" name="notes" :value="$deliveryOrder->notes" /></div>
            <button class="btn btn-primary btn-nb">Simpan</button>
            <a href="{{ route('delivery-orders.show', $deliveryOrder) }}" class="btn btn-outline btn-nb">Batal</a>
        </form>
    </x-card>
</x-app-layout>
