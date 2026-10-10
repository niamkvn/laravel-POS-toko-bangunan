<x-app-layout title="Gudang">
    <x-card>
        <x-slot:actions><a href="{{ route('warehouses.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Tambah Gudang</a></x-slot:actions>
        @if($warehouses->isEmpty())
            <x-empty-state title="Belum ada gudang" action-label="Tambah Gudang" :action-href="route('warehouses.create')" />
        @else
        <x-table>
            <thead><tr><th>Kode</th><th>Nama</th><th>Default</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @foreach($warehouses as $w)
                <tr>
                    <td class="fw-bold">{{ $w->code }}</td>
                    <td>{{ $w->name }}</td>
                    <td>@if($w->is_default)<x-badge variant="deep">Default</x-badge>@endif</td>
                    <td><x-badge :variant="$w->active ? 'mint' : 'danger'">{{ $w->active ? 'Aktif' : 'Nonaktif' }}</x-badge></td>
                    <td class="text-end">
                        <a href="{{ route('warehouses.edit', $w) }}" class="btn btn-sm btn-outline btn-nb"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('warehouses.destroy', $w) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus gudang ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger btn-nb"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$warehouses" />
        @endif
    </x-card>
</x-app-layout>
