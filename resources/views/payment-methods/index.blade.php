<x-app-layout title="Payment Method">
    <x-card>
        <x-slot:actions><a href="{{ route('payment-methods.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Tambah</a></x-slot:actions>
        @if($paymentMethods->isEmpty())
            <x-empty-state title="Belum ada metode pembayaran" action-label="Tambah" :action-href="route('payment-methods.create')" />
        @else
        <x-table>
            <thead><tr><th>Kode</th><th>Nama</th><th>Tipe</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @foreach($paymentMethods as $pm)
                <tr>
                    <td class="fw-bold">{{ $pm->code }}</td>
                    <td>{{ $pm->name }}</td>
                    <td><x-badge>{{ strtoupper($pm->type) }}</x-badge></td>
                    <td><x-badge :variant="$pm->active ? 'mint' : 'danger'">{{ $pm->active ? 'Aktif' : 'Nonaktif' }}</x-badge></td>
                    <td class="text-end">
                        <a href="{{ route('payment-methods.edit', $pm) }}" class="btn btn-sm btn-outline btn-nb"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('payment-methods.destroy', $pm) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger btn-nb"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$paymentMethods" />
        @endif
    </x-card>
</x-app-layout>
