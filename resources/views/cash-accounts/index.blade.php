<x-app-layout title="Kas">
    <x-card title="Kas &amp; Bank">
        <x-slot:actions><a href="{{ route('cash-accounts.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Tambah Akun</a></x-slot:actions>
        <x-table>
            <thead><tr><th>Kode</th><th>Nama</th><th>Tipe</th><th>Saldo</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @foreach($cashAccounts as $ca)
                <tr>
                    <td class="fw-bold">{{ $ca->code }}</td>
                    <td>{{ $ca->name }}</td>
                    <td><x-badge>{{ strtoupper($ca->type) }}</x-badge></td>
                    <td class="fw-bold">Rp {{ number_format($ca->current_balance,0,',','.') }}</td>
                    <td><x-badge :variant="$ca->active ? 'mint' : 'danger'">{{ $ca->active ? 'Aktif' : 'Nonaktif' }}</x-badge></td>
                    <td class="text-end">
                        <a href="{{ route('cash-accounts.edit', $ca) }}" class="btn btn-sm btn-outline btn-nb"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('cash-accounts.destroy', $ca) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger btn-nb"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$cashAccounts" />
    </x-card>
</x-app-layout>
