<x-app-layout title="Laporan Produk">
    <x-card title="Penjualan per Produk">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-5"><x-date-filter :from="$from" :to="$to" /></div>
            <div class="col-md-2"><x-field-button><button class="btn btn-outline btn-nb w-100">Filter</button></x-field-button></div>
        </form>
        @if($items->isEmpty())
            <x-empty-state title="Tidak ada data pada periode ini" />
        @else
        <x-table>
            <thead><tr><th>Produk</th><th>Qty Terjual</th><th>Total Penjualan</th><th>Laba Kotor</th></tr></thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td>{{ $item->total_qty }}</td>
                    <td>Rp {{ number_format($item->total_sales,0,',','.') }}</td>
                    <td class="fw-bold">Rp {{ number_format($item->gross_profit,0,',','.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        @endif
    </x-card>
</x-app-layout>
