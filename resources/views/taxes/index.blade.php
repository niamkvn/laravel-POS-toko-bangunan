<x-app-layout title="Pajak">
    <x-card>
        <x-slot:actions><a href="{{ route('taxes.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Tambah Pajak</a></x-slot:actions>
        @if($taxes->isEmpty())
            <x-empty-state title="Belum ada pajak" action-label="Tambah Pajak" :action-href="route('taxes.create')" />
        @else
        <x-table>
            <thead><tr><th>Nama</th><th>Rate</th><th>Default</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @foreach($taxes as $tax)
                <tr>
                    <td class="fw-bold">{{ $tax->name }}</td>
                    <td>{{ $tax->rate }}%</td>
                    <td>@if($tax->is_default)<x-badge variant="deep">Default</x-badge>@endif</td>
                    <td><x-badge :variant="$tax->active ? 'mint' : 'danger'">{{ $tax->active ? 'Aktif' : 'Nonaktif' }}</x-badge></td>
                    <td class="text-end">
                        <a href="{{ route('taxes.edit', $tax) }}" class="btn btn-sm btn-outline btn-nb"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('taxes.destroy', $tax) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pajak ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger btn-nb"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$taxes" />
        @endif
    </x-card>
</x-app-layout>
