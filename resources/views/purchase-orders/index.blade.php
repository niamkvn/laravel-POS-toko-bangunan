<x-app-layout title="Purchase Order">
    <x-card title="Daftar Purchase Order">
        <x-slot:actions>@can('purchase_order.create')<a href="{{ route('purchase-orders.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Buat PO</a>@endcan</x-slot:actions>

        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4"><x-search name="search" :value="request('search')" placeholder="Cari No. PO..." /></div>
            <div class="col-md-3">
                <x-select name="status" :options="['draft'=>'Draft','submitted'=>'Submitted','approved'=>'Approved','partially_received'=>'Partially Received','received'=>'Received','cancelled'=>'Cancelled']" :selected="request('status')" placeholder="Semua Status" auto-submit />
            </div>
        </form>

        @if($orders->isEmpty())
            <x-empty-state title="Belum ada Purchase Order" action-label="Buat PO" :action-href="route('purchase-orders.create')" />
        @else
        <x-table>
            <thead><tr><th>No. PO</th><th>Supplier</th><th>Tanggal</th><th>Total</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($orders as $po)
                <tr>
                    <td><a href="{{ route('purchase-orders.show', $po) }}">{{ $po->po_number }}</a></td>
                    <td>{{ $po->supplier->name ?? '-' }}</td>
                    <td class="small">{{ $po->order_date->format('d/m/Y') }}</td>
                    <td>Rp {{ number_format($po->total,0,',','.') }}</td>
                    <td><x-badge :variant="$po->status==='received'?'mint':($po->status==='cancelled'?'danger':'soft')">{{ ucfirst(str_replace('_',' ',$po->status)) }}</x-badge></td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$orders" />
        @endif
    </x-card>
</x-app-layout>
