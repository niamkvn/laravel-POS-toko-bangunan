<x-app-layout title="Pembelian">
    <x-card title="Daftar Pembelian">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4"><x-search name="search" :value="request('search')" placeholder="Cari No. Pembelian..." /></div>
            <div class="col-md-3">
                <x-select name="status" :options="['unpaid'=>'Unpaid','partial'=>'Partial','paid'=>'Paid']" :selected="request('status')" placeholder="Semua Status" auto-submit />
            </div>
        </form>

        @if($purchases->isEmpty())
            <x-empty-state title="Belum ada pembelian" />
        @else
        <x-table>
            <thead><tr><th>No. Pembelian</th><th>Supplier</th><th>Tanggal</th><th>Total</th><th>Dibayar</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($purchases as $p)
                <tr>
                    <td><a href="{{ route('purchases.show', $p) }}">{{ $p->purchase_number }}</a></td>
                    <td>{{ $p->supplier->name ?? '-' }}</td>
                    <td class="small">{{ $p->purchase_date->format('d/m/Y') }}</td>
                    <td>Rp {{ number_format($p->total,0,',','.') }}</td>
                    <td>Rp {{ number_format($p->paid_amount,0,',','.') }}</td>
                    <td><x-badge :variant="$p->status==='paid'?'mint':($p->status==='partial'?'warning':'danger')">{{ ucfirst($p->status) }}</x-badge></td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$purchases" />
        @endif
    </x-card>
</x-app-layout>
