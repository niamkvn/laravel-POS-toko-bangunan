<x-app-layout :title="$purchase->purchase_number">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0">{{ $purchase->purchase_number }}</h5>
            <p class="text-muted small mb-0">{{ $purchase->supplier->name ?? '-' }} — {{ $purchase->purchase_date->format('d/m/Y') }}</p>
        </div>
        @if($purchase->outstanding() > 0)
        <button class="btn btn-primary btn-nb" data-bs-toggle="modal" data-bs-target="#payModal">Bayar Hutang</button>
        @endif
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <x-card title="Item Pembelian">
                <x-table>
                    <thead><tr><th>Produk</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr></thead>
                    <tbody>
                        @foreach($purchase->items as $item)
                        <tr>
                            <td>{{ $item->product->name ?? '-' }}</td>
                            <td>{{ $item->qty }} {{ $item->unit->code ?? '' }}</td>
                            <td>Rp {{ number_format($item->unit_cost,0,',','.') }}</td>
                            <td>Rp {{ number_format($item->subtotal,0,',','.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </x-table>
            </x-card>
        </div>
        <div class="col-lg-4">
            <x-card title="Ringkasan">
                <div class="d-flex justify-content-between small py-1"><span>Total</span><span>Rp {{ number_format($purchase->total,0,',','.') }}</span></div>
                <div class="d-flex justify-content-between small py-1"><span>Dibayar</span><span>Rp {{ number_format($purchase->paid_amount,0,',','.') }}</span></div>
                <div class="d-flex justify-content-between fw-bold py-1 text-danger"><span>Outstanding</span><span>Rp {{ number_format($purchase->outstanding(),0,',','.') }}</span></div>
                <div class="d-flex justify-content-between small py-1"><span>Jatuh Tempo</span><span>{{ $purchase->due_date?->format('d/m/Y') }}</span></div>
            </x-card>
            <x-card title="Riwayat Pembayaran" class="mt-3">
                @forelse($purchase->payments as $pay)
                <div class="d-flex justify-content-between small border-bottom py-1">
                    <span>{{ $pay->payment_date->format('d/m/Y') }} — {{ $pay->cashAccount->name ?? '-' }}</span>
                    <span>Rp {{ number_format($pay->amount,0,',','.') }}</span>
                </div>
                @empty
                <p class="small text-muted mb-0">Belum ada pembayaran.</p>
                @endforelse
            </x-card>
        </div>
    </div>

    <x-modal id="payModal" title="Bayar Hutang">
        <form method="POST" action="{{ route('purchases.pay', $purchase) }}">
            @csrf
            <div class="mb-2">
                <label class="form-label">Jumlah Bayar</label>
                <input type="number" step="0.01" name="amount" class="form-control" max="{{ $purchase->outstanding() }}" value="{{ $purchase->outstanding() }}" required>
            </div>
            <div class="mb-2">
                <x-select label="Kas / Bank" name="cash_account_id" :options="\App\Models\CashAccount::where('active',true)->pluck('name','id')" placeholder="-- Pilih --" required />
            </div>
            <div class="mb-2">
                <label class="form-label">No. Referensi</label>
                <input type="text" name="reference_number" class="form-control">
            </div>
            <button class="btn btn-primary btn-nb w-100">Simpan Pembayaran</button>
        </form>
    </x-modal>
</x-app-layout>
