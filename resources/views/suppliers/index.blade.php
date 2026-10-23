<x-app-layout title="Supplier">
    <x-card>
        <x-slot:actions>@can('supplier.create')<a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Tambah Supplier</a>@endcan</x-slot:actions>
        <form method="GET" class="mb-3" style="max-width:320px;"><x-search name="search" :value="request('search')" placeholder="Cari nama/kode..." /></form>
        @if($suppliers->isEmpty())
            <x-empty-state title="Belum ada supplier" action-label="Tambah Supplier" :action-href="route('suppliers.create')" />
        @else
        <x-table>
            <thead><tr><th>Kode</th><th>Nama</th><th>Telepon</th><th>Termin</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @foreach($suppliers as $s)
                <tr>
                    <td class="fw-bold">{{ $s->supplier_code }}</td>
                    <td><a href="{{ route('suppliers.show', $s) }}">{{ $s->name }}</a></td>
                    <td>{{ $s->phone }}</td>
                    <td>{{ $s->payment_term_days }} hari</td>
                    <td class="text-end">
                        <a href="{{ route('suppliers.edit', $s) }}" class="btn btn-sm btn-outline btn-nb"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('suppliers.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger btn-nb"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$suppliers" />
        @endif
    </x-card>
</x-app-layout>
