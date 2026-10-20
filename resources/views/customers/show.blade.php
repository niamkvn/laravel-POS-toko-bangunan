<x-app-layout :title="$customer->name">
    <div class="row g-3 mb-3">
        <div class="col-md-4"><x-stat-card label="Limit Kredit" value="Rp {{ number_format($customer->credit_limit,0,',','.') }}" icon="bi-credit-card" variant="primary" /></div>
        <div class="col-md-4"><x-stat-card label="Total Piutang" value="Rp {{ number_format($customer->totalReceivable(),0,',','.') }}" icon="bi-wallet2" variant="warning" /></div>
        <div class="col-md-4"><x-stat-card label="Termin" value="{{ $customer->payment_term_days }} Hari" icon="bi-calendar-check" variant="soft" /></div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <x-card :title="$customer->name" :subtitle="$customer->customer_code">
                <p class="mb-1"><strong>Tipe:</strong> {{ ucfirst($customer->customer_type) }}</p>
                <p class="mb-1"><strong>Telepon:</strong> {{ $customer->phone ?? '-' }}</p>
                <p class="mb-1"><strong>Email:</strong> {{ $customer->email ?? '-' }}</p>
                <p class="mb-0"><strong>Alamat:</strong> {{ $customer->address ?? '-' }}</p>
            </x-card>

            <x-card title="Riwayat Transaksi" class="mt-3">
                @if($sales->isEmpty())
                    <p class="text-muted small mb-0">Belum ada transaksi.</p>
                @else
                <x-table>
                    <thead><tr><th>Invoice</th><th>Tanggal</th><th>Total</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($sales as $s)
                        <tr>
                            <td><a href="{{ route('sales.show', $s) }}">{{ $s->invoice_number }}</a></td>
                            <td>{{ $s->sale_date->format('d/m/Y') }}</td>
                            <td>Rp {{ number_format($s->total,0,',','.') }}</td>
                            <td><x-badge :variant="$s->status==='void'?'danger':'mint'">{{ ucfirst($s->status) }}</x-badge></td>
                        </tr>
                        @endforeach
                    </tbody>
                </x-table>
                <x-pagination :paginator="$sales" />
                @endif
            </x-card>
        </div>

        <div class="col-lg-4">
            <x-card title="Harga Khusus (Customer Specific)">
                <form method="POST" action="{{ route('customers.special-prices.store', $customer) }}" class="mb-3">
                    @csrf
                    <div class="mb-2"><x-select name="product_id" :options="$products" placeholder="Pilih produk" required /></div>
                    <div class="row g-2">
                        <div class="col-6"><input type="number" step="0.001" name="min_qty" class="form-control form-control-sm" placeholder="Min Qty" value="1" required></div>
                        <div class="col-6"><input type="number" step="0.01" name="price" class="form-control form-control-sm" placeholder="Harga" required></div>
                    </div>
                    <button class="btn btn-sm btn-primary btn-nb mt-2 w-100">Tambah</button>
                </form>
                @forelse($customer->specialPrices as $sp)
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <div>
                            <div class="small fw-bold">{{ $sp->product->name ?? '-' }}</div>
                            <div class="small text-muted">Min {{ $sp->min_qty }}</div>
                        </div>
                        <div class="text-end">
                            <div class="small fw-bold">Rp {{ number_format($sp->price,0,',','.') }}</div>
                            <form action="{{ route('customers.special-prices.destroy', $sp) }}" method="POST" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm text-danger p-0 border-0 bg-transparent"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="small text-muted mb-0">Belum ada harga khusus.</p>
                @endforelse
            </x-card>

            <x-card title="Project" class="mt-3">
                @forelse($customer->projects as $project)
                    <div class="border-bottom py-2">
                        <div class="small fw-bold">{{ $project->name }}</div>
                        <div class="small text-muted">{{ $project->project_code }} — {{ ucfirst($project->status) }}</div>
                    </div>
                @empty
                    <p class="small text-muted mb-0">Belum ada project.</p>
                @endforelse
            </x-card>
        </div>
    </div>
</x-app-layout>
