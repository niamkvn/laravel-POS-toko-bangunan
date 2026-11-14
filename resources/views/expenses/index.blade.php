<x-app-layout title="Pengeluaran">
    <x-card title="Pengeluaran">
        <x-slot:actions>@can('expense.create')<a href="{{ route('expenses.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Catat Pengeluaran</a>@endcan</x-slot:actions>
        <form method="GET" class="mb-3"><x-date-filter :from="request('date_from')" :to="request('date_to')" /></form>
        @if($expenses->isEmpty())
            <x-empty-state title="Belum ada pengeluaran" action-label="Catat Pengeluaran" :action-href="route('expenses.create')" />
        @else
        <x-table>
            <thead><tr><th>No.</th><th>Kategori</th><th>Kas</th><th>Tanggal</th><th>Deskripsi</th><th>Jumlah</th></tr></thead>
            <tbody>
                @foreach($expenses as $e)
                <tr>
                    <td><a href="{{ route('expenses.show', $e) }}">{{ $e->expense_number }}</a></td>
                    <td>{{ $e->category->name ?? '-' }}</td>
                    <td>{{ $e->cashAccount->name ?? '-' }}</td>
                    <td class="small">{{ $e->expense_date->format('d/m/Y') }}</td>
                    <td class="small">{{ $e->description }}</td>
                    <td class="fw-bold">Rp {{ number_format($e->amount,0,',','.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$expenses" />
        @endif
    </x-card>
</x-app-layout>
