<x-app-layout title="Satuan">
    <x-card>
        <x-slot:actions><a href="{{ route('units.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Tambah Satuan</a></x-slot:actions>
        @if($units->isEmpty())
            <x-empty-state title="Belum ada satuan" action-label="Tambah Satuan" :action-href="route('units.create')" />
        @else
        <x-table>
            <thead><tr><th>Kode</th><th>Nama</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @foreach($units as $unit)
                <tr>
                    <td class="fw-bold">{{ $unit->code }}</td>
                    <td>{{ $unit->name }}</td>
                    <td class="text-end">
                        <a href="{{ route('units.edit', $unit) }}" class="btn btn-sm btn-outline btn-nb"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('units.destroy', $unit) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus satuan ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger btn-nb"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$units" />
        @endif
    </x-card>
</x-app-layout>
