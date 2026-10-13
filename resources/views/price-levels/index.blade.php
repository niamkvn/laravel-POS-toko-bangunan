<x-app-layout title="Price List">
    <x-card>
        <x-slot:actions><a href="{{ route('price-levels.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Tambah Level</a></x-slot:actions>
        @if($priceLevels->isEmpty())
            <x-empty-state title="Belum ada price level" action-label="Tambah Level" :action-href="route('price-levels.create')" />
        @else
        <x-table>
            <thead><tr><th>Kode</th><th>Nama</th><th>Deskripsi</th><th>Default</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @foreach($priceLevels as $pl)
                <tr>
                    <td class="fw-bold">{{ $pl->code }}</td>
                    <td>{{ $pl->name }}</td>
                    <td class="text-muted small">{{ $pl->description }}</td>
                    <td>@if($pl->is_default)<x-badge variant="deep">Default</x-badge>@endif</td>
                    <td class="text-end">
                        <a href="{{ route('price-levels.edit', $pl) }}" class="btn btn-sm btn-outline btn-nb"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('price-levels.destroy', $pl) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger btn-nb"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$priceLevels" />
        @endif
    </x-card>
</x-app-layout>
