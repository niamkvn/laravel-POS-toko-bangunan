<x-app-layout :title="$supplier->name">
    <div class="row g-3 mb-3">
        <div class="col-md-6"><x-stat-card label="Total Hutang" value="Rp {{ number_format($supplier->totalPayable(),0,',','.') }}" icon="bi-credit-card" variant="warning" /></div>
        <div class="col-md-6"><x-stat-card label="Termin Pembayaran" value="{{ $supplier->payment_term_days }} Hari" icon="bi-calendar-check" variant="soft" /></div>
    </div>

    <x-card :title="$supplier->name" :subtitle="$supplier->supplier_code">
        <p class="mb-1"><strong>Telepon:</strong> {{ $supplier->phone ?? '-' }}</p>
        <p class="mb-1"><strong>Email:</strong> {{ $supplier->email ?? '-' }}</p>
        <p class="mb-0"><strong>Alamat:</strong> {{ $supplier->address ?? '-' }}</p>
    </x-card>

    <x-card title="Riwayat Pembelian" class="mt-3">
        @if($purchases->isEmpty())
            <p class="text-muted small mb-0">Belum ada transaksi.</p>
        @else
        <x-table>
            <thead><tr><th>No. Pembelian</th><th>Tanggal</th><th>Total</th><th>Outstanding</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($purchases as $p)
                <tr>
                    <td><a href="{{ route('purchases.show', $p) }}">{{ $p->purchase_number }}</a></td>
                    <td>{{ $p->purchase_date->format('d/m/Y') }}</td>
                    <td>Rp {{ number_format($p->total,0,',','.') }}</td>
                    <td>Rp {{ number_format($p->outstanding(),0,',','.') }}</td>
                    <td><x-badge :variant="$p->status==='paid'?'mint':($p->status==='partial'?'warning':'danger')">{{ ucfirst($p->status) }}</x-badge></td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$purchases" />
        @endif
    </x-card>
</x-app-layout>
