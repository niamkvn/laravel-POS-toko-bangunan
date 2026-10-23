<x-app-layout title="Hutang Supplier">
    <x-card title="Daftar Hutang Supplier">
        <form method="GET" class="mb-3" style="max-width:320px;">
            <x-select name="supplier_id" :options="$suppliers" :selected="request('supplier_id')" placeholder="Semua Supplier" auto-submit />
        </form>

        @if($purchases->isEmpty())
            <x-empty-state title="Tidak ada hutang" description="Semua pembelian sudah lunas." />
        @else
        <x-table>
            <thead><tr><th>No. Pembelian</th><th>Supplier</th><th>Jatuh Tempo</th><th>Total</th><th>Outstanding</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @foreach($purchases as $p)
                <tr class="{{ $p->isOverdue() ? 'diff-negative' : '' }}">
                    <td><a href="{{ route('purchases.show', $p) }}">{{ $p->purchase_number }}</a></td>
                    <td>{{ $p->supplier->name ?? '-' }}</td>
                    <td>{{ $p->due_date?->format('d/m/Y') ?? '-' }}</td>
                    <td>Rp {{ number_format($p->total,0,',','.') }}</td>
                    <td class="fw-bold">Rp {{ number_format($p->outstanding(),0,',','.') }}</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-primary btn-nb" data-bs-toggle="modal" data-bs-target="#pay-{{ $p->id }}">Bayar</button>
                    </td>
                </tr>
                <x-modal id="pay-{{ $p->id }}" title="Bayar Hutang — {{ $p->purchase_number }}">
                    <form method="POST" action="{{ route('purchases.pay', $p) }}">
                        @csrf
                        <p class="mb-2">Outstanding: <strong>Rp {{ number_format($p->outstanding(),0,',','.') }}</strong></p>
                        <div class="mb-2">
                            <label class="form-label">Jumlah Bayar</label>
                            <input type="number" step="0.01" name="amount" class="form-control" max="{{ $p->outstanding() }}" value="{{ $p->outstanding() }}" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Kas / Bank</label>
                            <select name="cash_account_id" class="form-select" required>
                                @foreach(\App\Models\CashAccount::where('active',true)->get() as $ca)
                                    <option value="{{ $ca->id }}">{{ $ca->name }}</option>
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
