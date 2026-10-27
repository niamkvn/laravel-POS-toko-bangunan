<x-app-layout title="Quotation">
    <x-card title="Daftar Quotation">
        <x-slot:actions>@can('quotation.create')<a href="{{ route('quotations.create') }}" class="btn btn-primary btn-nb"><i class="bi bi-plus-lg"></i> Buat Quotation</a>@endcan</x-slot:actions>
        @if($quotations->isEmpty())
            <x-empty-state title="Belum ada quotation" action-label="Buat Quotation" :action-href="route('quotations.create')" />
        @else
        <x-table>
            <thead><tr><th>No.</th><th>Customer</th><th>Tanggal</th><th>Total</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($quotations as $q)
                <tr>
                    <td><a href="{{ route('quotations.show', $q) }}">{{ $q->number }}</a></td>
                    <td>{{ $q->customer->name ?? '-' }}</td>
                    <td class="small">{{ $q->date->format('d/m/Y') }}</td>
                    <td>Rp {{ number_format($q->total,0,',','.') }}</td>
                    <td><x-badge :variant="$q->status==='converted'?'mint':($q->status==='rejected'?'danger':'soft')">{{ ucfirst($q->status) }}</x-badge></td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$quotations" />
        @endif
    </x-card>
</x-app-layout>
