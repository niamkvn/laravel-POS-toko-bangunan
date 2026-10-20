<x-app-layout title="Piutang">
    <div class="row g-3 mb-3">
        <div class="col-md-3"><x-stat-card label="Current" value="Rp {{ number_format($aging['current'],0,',','.') }}" variant="soft" /></div>
        <div class="col-md-2"><x-stat-card label="1-30 Hari" value="Rp {{ number_format($aging['1_30'],0,',','.') }}" variant="mint" /></div>
        <div class="col-md-2"><x-stat-card label="31-60 Hari" value="Rp {{ number_format($aging['31_60'],0,',','.') }}" variant="warning" /></div>
        <div class="col-md-2"><x-stat-card label="61-90 Hari" value="Rp {{ number_format($aging['61_90'],0,',','.') }}" variant="warning" /></div>
        <div class="col-md-3"><x-stat-card label="&gt;90 Hari" value="Rp {{ number_format($aging['over_90'],0,',','.') }}" variant="danger" /></div>
    </div>

    <x-card title="Daftar Piutang">
        <form method="GET" class="mb-3" style="max-width:320px;">
            <x-select name="customer_id" :options="$customers" :selected="request('customer_id')" placeholder="Semua Customer" auto-submit />
        </form>

        @if($sales->isEmpty())
            <x-empty-state title="Tidak ada piutang" description="Semua transaksi kredit sudah lunas." />
        @else
        <x-table>
            <thead><tr><th>Invoice</th><th>Customer</th><th>Jatuh Tempo</th><th>Total</th><th>Outstanding</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @foreach($sales as $s)
                <tr class="{{ $s->isOverdue() ? 'diff-negative' : '' }}">
                    <td><a href="{{ route('sales.show', $s) }}">{{ $s->invoice_number }}</a></td>
                    <td>{{ $s->customer->name ?? '-' }}</td>
                    <td>{{ $s->due_date?->format('d/m/Y') ?? '-' }}</td>
                    <td>Rp {{ number_format($s->total,0,',','.') }}</td>
                    <td class="fw-bold">Rp {{ number_format($s->outstanding(),0,',','.') }}</td>
                    <td>@if($s->isOverdue())<x-badge variant="danger">Overdue</x-badge>@else<x-badge variant="mint">Berjalan</x-badge>@endif</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-primary btn-nb" data-bs-toggle="modal" data-bs-target="#pay-{{ $s->id }}">Bayar</button>
                    </td>
                </tr>
                <x-modal id="pay-{{ $s->id }}" title="Terima Pembayaran — {{ $s->invoice_number }}">
                    <form method="POST" action="{{ route('customers.receive-payment', $s) }}">
                        @csrf
                        <p class="mb-2">Outstanding: <strong>Rp {{ number_format($s->outstanding(),0,',','.') }}</strong></p>
                        <div class="mb-2">
                            <label class="form-label">Jumlah Bayar</label>
                            <input type="number" step="0.01" name="amount" class="form-control" max="{{ $s->outstanding() }}" value="{{ $s->outstanding() }}" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Metode Pembayaran</label>
                            <select name="payment_method_id" class="form-select" required>
                                @foreach(\App\Models\PaymentMethod::where('active',true)->get() as $pm)
                                    <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">No. Referensi</label>
                            <input type="text" name="reference_number" class="form-control">
                        </div>
                        <button class="btn btn-primary btn-nb w-100">Simpan Pembayaran</button>
                    </form>
                </x-modal>
                @endforeach
            </tbody>
        </x-table>
        @endif
    </x-card>
</x-app-layout>
