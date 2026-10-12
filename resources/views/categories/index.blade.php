<x-app-layout title="Kategori">
    <x-card>
        <x-slot:actions>
            @can('category.create')
            <a href="{{ route('categories.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Tambah Kategori</a>
            @endcan
        </x-slot:actions>

        <form method="GET" class="mb-3" style="max-width:320px;">
            <x-search name="search" :value="request('search')" placeholder="Cari kategori..." />
        </form>

        @if($categories->isEmpty())
            <x-empty-state title="Belum ada kategori" description="Tambahkan kategori untuk mengelompokkan produk." action-label="Tambah Kategori" :action-href="route('categories.create')" />
        @else
        <x-table>
            <thead><tr><th>Nama</th><th>Induk</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @foreach($categories as $category)
                <tr>
                    <td class="fw-bold">{{ $category->name }}</td>
                    <td>{{ $category->parent->name ?? '-' }}</td>
                    <td><x-badge :variant="$category->active ? 'mint' : 'danger'">{{ $category->active ? 'Aktif' : 'Nonaktif' }}</x-badge></td>
                    <td class="text-end">
                        @can('category.edit')
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline btn-nb"><i class="bi bi-pencil"></i></a>
                        @endcan
                        @can('category.delete')
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" x-data @submit="return confirm('Hapus kategori ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger btn-nb"><i class="bi bi-trash"></i></button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$categories" />
        @endif
    </x-card>
</x-app-layout>
