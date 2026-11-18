<x-app-layout title="Laporan Customer">
    <x-card title="Ranking Customer">
        <x-table>
            <thead><tr><th>Customer</th><th>Tipe</th><th>Jumlah Transaksi</th><th>Total Pembelian</th></tr></thead>
            <tbody>
                @foreach($customers as $c)
                <tr>
                    <td><a href="{{ route('customers.show', $c) }}">{{ $c->name }}</a></td>
                    <td><x-badge>{{ ucfirst($c->customer_type) }}</x-badge></td>
                    <td>{{ $c->sales_count }}</td>
                    <td class="fw-bold">Rp {{ number_format($c->total_purchase ?? 0,0,',','.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$customers" />
    </x-card>
</x-app-layout>
