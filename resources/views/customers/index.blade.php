<x-app-layout title="Customer">
    <x-card>
        <x-slot:actions>@can('customer.create')<a href="{{ route('customers.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Tambah Customer</a>@endcan</x-slot:actions>

        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4"><x-search name="search" :value="request('search')" placeholder="Cari nama/kode..." /></div>
            <div class="col-md-3">
                <x-select name="customer_type" :options="['umum'=>'Umum','tukang'=>'Tukang','reseller'=>'Reseller','kontraktor'=>'Kontraktor','developer'=>'Developer','perusahaan'=>'Perusahaan']" :selected="request('customer_type')" placeholder="Semua Tipe" auto-submit />
            </div>
        </form>

        @if($customers->isEmpty())
            <x-empty-state title="Belum ada customer" action-label="Tambah Customer" :action-href="route('customers.create')" />
        @else
        <x-table>
            <thead><tr><th>Kode</th><th>Nama</th><th>Tipe</th><th>Telepon</th><th>Limit Kredit</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @foreach($customers as $c)
                <tr>
                    <td class="fw-bold">{{ $c->customer_code }}</td>
                    <td><a href="{{ route('customers.show', $c) }}">{{ $c->name }}</a></td>
                    <td><x-badge>{{ ucfirst($c->customer_type) }}</x-badge></td>
                    <td>{{ $c->phone }}</td>
                    <td>Rp {{ number_format($c->credit_limit, 0, ',', '.') }}</td>
                    <td class="text-end">
                        <a href="{{ route('customers.edit', $c) }}" class="btn btn-sm btn-outline btn-nb"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('customers.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger btn-nb"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$customers" />
        @endif
    </x-card>
</x-app-layout>
