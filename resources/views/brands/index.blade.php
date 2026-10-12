<x-app-layout title="Brand">
    <x-card>
        <x-slot:actions>
            @can('category.create')<a href="{{ route('brands.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Tambah Brand</a>@endcan
        </x-slot:actions>
        <form method="GET" class="mb-3" style="max-width:320px;">
            <x-search name="search" :value="request('search')" placeholder="Cari brand..." />
        </form>
        @if($brands->isEmpty())
            <x-empty-state title="Belum ada brand" action-label="Tambah Brand" :action-href="route('brands.create')" />
        @else
        <x-table>
            <thead><tr><th>Nama</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @foreach($brands as $brand)
                <tr>
                    <td class="fw-bold">{{ $brand->name }}</td>
                    <td><x-badge :variant="$brand->active ? 'mint' : 'danger'">{{ $brand->active ? 'Aktif' : 'Nonaktif' }}</x-badge></td>
                    <td class="text-end">
                        <a href="{{ route('brands.edit', $brand) }}" class="btn btn-sm btn-outline btn-nb"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('brands.destroy', $brand) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus brand ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger btn-nb"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$brands" />
        @endif
    </x-card>
</x-app-layout>
