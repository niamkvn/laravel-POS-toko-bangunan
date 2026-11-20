<x-app-layout title="Users">
    <x-card>
        <x-slot:actions><a href="{{ route('users.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Tambah User</a></x-slot:actions>
        <form method="GET" class="mb-3" style="max-width:320px;"><x-search name="search" :value="request('search')" placeholder="Cari nama..." /></form>
        <x-table>
            <thead><tr><th>Kode</th><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @foreach($users as $u)
                <tr>
                    <td class="fw-bold">{{ $u->employee_code }}</td>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td><x-badge>{{ $u->roles->pluck('name')->join(', ') ?: '-' }}</x-badge></td>
                    <td><x-badge :variant="$u->active ? 'mint' : 'danger'">{{ $u->active ? 'Aktif' : 'Nonaktif' }}</x-badge></td>
                    <td class="text-end">
                        <a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-outline btn-nb"><i class="bi bi-pencil"></i></a>
                        @if($u->id !== auth()->id())
                        <form action="{{ route('users.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus user ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger btn-nb"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$users" />
    </x-card>
</x-app-layout>
