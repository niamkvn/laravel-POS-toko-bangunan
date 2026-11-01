<x-app-layout :title="$sale->invoice_number">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0">{{ $sale->invoice_number }}</h5>
            <p class="text-muted small mb-0">{{ $sale->sale_date->format('d F Y H:i') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sales.print', $sale) }}" target="_blank" class="btn btn-outline btn-nb"><i class="bi bi-printer"></i> Cetak Invoice</a>
            @can('sales.void')
            @if($sale->status === 'completed')
            <button class="btn btn-danger btn-nb" data-bs-toggle="modal" data-bs-target="#voidModal">Void</button>
            @endif
            @endcan
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <x-card title="Item Penjualan">
                <x-table>
                    <thead><tr><th>Produk</th><th>Qty</th><th>Harga</th><th>Diskon</th><th>Subtotal</th></tr></thead>
                    <tbody>
                        @foreach($sale->items as $item)
                        <tr>
                            <td>{{ $item->product->name ?? '-' }}</td>
                            <td>{{ $item->qty }} {{ $item->unit->code ?? '' }}</td>
                            <td>Rp {{ number_format($item->price,0,',','.') }}</td>
                            <td>Rp {{ number_format($item->discount_amount,0,',','.') }}</td>
                            <td>Rp {{ number_format($item->subtotal,0,',','.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </x-table>
            </x-card>

            @if($sale->returns->isNotEmpty())
            <x-card title="Retur" class="mt-3">
                @foreach($sale->returns as $ret)
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="small">{{ $ret->return_number }} — {{ $ret->return_date->format('d/m/Y') }}</span>
                    <span class="small fw-bold">Rp {{ number_format($ret->total,0,',','.') }}</span>
                </div>
                @endforeach
            </x-card>
            @endif
        </div>

        <div class="col-lg-4">
            <x-card title="Ringkasan">
                <div class="d-flex justify-content-between small py-1"><span>Subtotal</span><span>Rp {{ number_format($sale->subtotal,0,',','.') }}</span></div>
                <div class="d-flex justify-content-between small py-1"><span>Diskon</span><span>Rp {{ number_format($sale->discount_amount,0,',','.') }}</span></div>
                <div class="d-flex justify-content-between small py-1"><span>Pajak</span><span>Rp {{ number_format($sale->tax_amount,0,',','.') }}</span></div>
                <hr>
                <div class="d-flex justify-content-between fw-bold py-1"><span>Total</span><span>Rp {{ number_format($sale->total,0,',','.') }}</span></div>
                <div class="d-flex justify-content-between small py-1"><span>Dibayar</span><span>Rp {{ number_format($sale->paid_amount,0,',','.') }}</span></div>
                @if($sale->is_credit)
                <div class="d-flex justify-content-between small py-1 text-danger fw-bold"><span>Outstanding</span><span>Rp {{ number_format($sale->outstanding(),0,',','.') }}</span></div>
                @endif
            </x-card>

            <x-card title="Customer" class="mt-3">
                <p class="mb-0 small">{{ $sale->customer->name ?? 'Umum (Walk-in)' }}</p>
            </x-card>

            <x-card title="Pembayaran" class="mt-3">
                @foreach($sale->payments as $p)
                <div class="d-flex justify-content-between small border-bottom py-1">
                    <span>{{ $p->paymentMethod->name ?? '-' }}</span>
                    <span>Rp {{ number_format($p->amount,0,',','.') }}</span>
                </div>
                @endforeach
            </x-card>

            @if($sale->status === 'void')
            <x-card title="Void" class="mt-3">
                <p class="small mb-0">Alasan: {{ $sale->void_reason }}</p>
            </x-card>
            @endif
        </div>
    </div>

    <x-modal id="voidModal" title="Void Transaksi">
        <form method="POST" action="{{ route('pos.void', $sale) }}">
            @csrf
            <p class="small">Stok akan dikembalikan dan transaksi ditandai void.</p>
            <div class="mb-3">
                <label class="form-label">Alasan Void</label>
                <textarea name="reason" class="form-control" required></textarea>
            </div>
            <button class="btn btn-danger btn-nb w-100">Void Transaksi</button>
        </form>
    </x-modal>
</x-app-layout>
