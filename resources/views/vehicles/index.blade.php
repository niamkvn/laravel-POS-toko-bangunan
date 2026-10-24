<x-app-layout title="Kendaraan">
    <x-card>
        <x-slot:actions><a href="{{ route('vehicles.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Tambah Kendaraan</a></x-slot:actions>
        <form method="GET" class="mb-3" style="max-width:320px;"><x-search name="search" :value="request('search')" placeholder="Cari plat nomor..." /></form>
        @if($vehicles->isEmpty())
            <x-empty-state title="Belum ada kendaraan" action-label="Tambah Kendaraan" :action-href="route('vehicles.create')" />
        @else
        <x-table>
            <thead><tr><th>Kode</th><th>Plat Nomor</th><th>Tipe</th><th>Driver</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @foreach($vehicles as $v)
                <tr>
                    <td class="fw-bold">{{ $v->vehicle_code }}</td>
                    <td>{{ $v->plate_number }}</td>
                    <td>{{ $v->vehicle_type }}</td>
                    <td>{{ $v->driver->name ?? '-' }}</td>
                    <td><x-badge :variant="$v->active ? 'mint' : 'danger'">{{ $v->active ? 'Aktif' : 'Nonaktif' }}</x-badge></td>
                    <td class="text-end">
                        <a href="{{ route('vehicles.edit', $v) }}" class="btn btn-sm btn-outline btn-nb"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('vehicles.destroy', $v) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger btn-nb"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$vehicles" />
        @endif
    </x-card>
</x-app-layout>
