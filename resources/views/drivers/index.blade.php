<x-app-layout title="Driver">
    <x-card>
        <x-slot:actions><a href="{{ route('drivers.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Tambah Driver</a></x-slot:actions>
        <form method="GET" class="mb-3" style="max-width:320px;"><x-search name="search" :value="request('search')" placeholder="Cari driver..." /></form>
        @if($drivers->isEmpty())
            <x-empty-state title="Belum ada driver" action-label="Tambah Driver" :action-href="route('drivers.create')" />
        @else
        <x-table>
            <thead><tr><th>Kode</th><th>Nama</th><th>Telepon</th><th>SIM</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @foreach($drivers as $d)
                <tr>
                    <td class="fw-bold">{{ $d->driver_code }}</td>
                    <td>{{ $d->name }}</td>
                    <td>{{ $d->phone }}</td>
                    <td>{{ $d->license_number }}</td>
                    <td><x-badge :variant="$d->active ? 'mint' : 'danger'">{{ $d->active ? 'Aktif' : 'Nonaktif' }}</x-badge></td>
                    <td class="text-end">
                        <a href="{{ route('drivers.edit', $d) }}" class="btn btn-sm btn-outline btn-nb"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('drivers.destroy', $d) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger btn-nb"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$drivers" />
        @endif
    </x-card>
</x-app-layout>
