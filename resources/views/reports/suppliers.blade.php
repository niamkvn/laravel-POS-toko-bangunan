<x-app-layout title="Laporan Supplier">
    <x-card title="Ranking Supplier">
        <x-table>
            <thead><tr><th>Supplier</th><th>Jumlah Transaksi</th><th>Total Pembelian</th></tr></thead>
            <tbody>
                @foreach($suppliers as $s)
                <tr>
                    <td><a href="{{ route('suppliers.show', $s) }}">{{ $s->name }}</a></td>
                    <td>{{ $s->purchases_count }}</td>
                    <td class="fw-bold">Rp {{ number_format($s->total_purchase ?? 0,0,',','.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$suppliers" />
    </x-card>
</x-app-layout>
