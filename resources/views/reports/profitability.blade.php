<x-app-layout title="Laporan Profitability">
    <x-card title="Profitability per Produk">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-5"><x-date-filter :from="$from" :to="$to" /></div>
            <div class="col-md-2"><x-field-button><button class="btn btn-outline btn-nb w-100">Filter</button></x-field-button></div>
        </form>
        @if($items->isEmpty())
            <x-empty-state title="Tidak ada data pada periode ini" />
        @else
        <x-table>
            <thead><tr><th>Produk</th><th>Kategori</th><th>Revenue</th><th>Cost</th><th>Profit</th><th>Margin</th></tr></thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td>{{ $item->product->category->name ?? '-' }}</td>
                    <td>Rp {{ number_format($item->revenue,0,',','.') }}</td>
                    <td>Rp {{ number_format($item->cost,0,',','.') }}</td>
                    <td class="fw-bold">Rp {{ number_format($item->profit,0,',','.') }}</td>
                    <td><x-badge :variant="$item->margin >= 20 ? 'mint' : 'warning'">{{ number_format($item->margin,1) }}%</x-badge></td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        @endif
    </x-card>
</x-app-layout>
