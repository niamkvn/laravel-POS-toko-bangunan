<x-app-layout title="Laporan Project">
    <x-card title="Performa Project">
        <x-table>
            <thead><tr><th>Project</th><th>Customer</th><th>Status</th><th>Budget</th><th>Total Sales</th></tr></thead>
            <tbody>
                @foreach($projects as $p)
                <tr>
                    <td><a href="{{ route('customer-projects.show', $p) }}">{{ $p->name }}</a></td>
                    <td>{{ $p->customer->name ?? '-' }}</td>
                    <td><x-badge>{{ ucfirst($p->status) }}</x-badge></td>
                    <td>Rp {{ number_format($p->budget ?? 0,0,',','.') }}</td>
                    <td class="fw-bold">Rp {{ number_format($p->total_sales ?? 0,0,',','.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$projects" />
    </x-card>
</x-app-layout>
