<x-app-layout title="Roles">
    <x-card>
        <x-slot:actions><a href="{{ route('roles.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Tambah Role</a></x-slot:actions>
        <x-table>
            <thead><tr><th>Nama Role</th><th>Jumlah Permission</th><th>Jumlah User</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @foreach($roles as $r)
                <tr>
                    <td class="fw-bold">{{ $r->name }}</td>
                    <td>{{ $r->permissions_count }}</td>
                    <td>{{ $r->users_count }}</td>
                    <td class="text-end">
                        <a href="{{ route('roles.edit', $r) }}" class="btn btn-sm btn-outline btn-nb"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('roles.destroy', $r) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus role ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger btn-nb"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$roles" />
    </x-card>
</x-app-layout>
