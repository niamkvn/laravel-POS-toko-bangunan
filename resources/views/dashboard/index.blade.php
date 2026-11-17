<x-app-layout title="Dashboard">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
        <div>
            <h4 class="mb-1">Selamat datang kembali, {{ auth()->user()->name }}</h4>
            <p class="text-muted mb-0">Berikut ringkasan operasional toko hari ini.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-3">
            <x-stat-card label="Omzet Hari Ini" value="Rp {{ number_format($todaySales, 0, ',', '.') }}" icon="bi-cash-coin" variant="primary" />
        </div>
        <div class="col-6 col-md-4 col-xl-3">
            <x-stat-card label="Laba Kotor (Bulan)" value="Rp {{ number_format($grossProfit, 0, ',', '.') }}" icon="bi-graph-up-arrow" variant="mint" />
        </div>
        <div class="col-6 col-md-4 col-xl-3">
            <x-stat-card label="Piutang" value="Rp {{ number_format($receivable, 0, ',', '.') }}" icon="bi-wallet2" variant="soft" />
        </div>
        <div class="col-6 col-md-4 col-xl-3">
            <x-stat-card label="Hutang" value="Rp {{ number_format($payable, 0, ',', '.') }}" icon="bi-credit-card" variant="warning" />
        </div>
        <div class="col-6 col-md-4 col-xl-3">
            <x-stat-card label="Stok Kritis" value="{{ $lowStock->count() }} Produk" icon="bi-exclamation-triangle" variant="danger" />
        </div>
        <div class="col-6 col-md-4 col-xl-3">
            <x-stat-card label="Stok Habis" value="{{ $outOfStock->count() }} Produk" icon="bi-x-octagon" variant="danger" />
        </div>
        <div class="col-6 col-md-4 col-xl-3">
            <x-stat-card label="Nilai Inventory" value="Rp {{ number_format($inventoryValue, 0, ',', '.') }}" icon="bi-boxes" variant="white" />
        </div>
        <div class="col-6 col-md-4 col-xl-3">
            <x-stat-card label="Saldo Kas" value="Rp {{ number_format($cashBalance, 0, ',', '.') }}" icon="bi-bank" variant="white" />
        </div>
    </div>

    <x-card class="mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h6 class="mb-0">Ringkasan Periode: {{ $periodLabel }}</h6>
            <form method="GET" class="d-flex flex-wrap gap-2 align-items-center">
                @php $period = request('period', 'this_month'); @endphp
                @foreach(['today'=>'Hari Ini','yesterday'=>'Kemarin','this_week'=>'Minggu Ini','this_month'=>'Bulan Ini','last_month'=>'Bulan Lalu'] as $k=>$v)
                    <button type="submit" name="period" value="{{ $k }}" class="btn btn-sm {{ $period===$k ? 'btn-primary' : 'btn-outline' }} btn-nb">{{ $v }}</button>
                @endforeach
                <div style="width:160px;"><x-input type="date" name="date_from" :value="request('date_from')" /></div>
                <div style="width:160px;"><x-input type="date" name="date_to" :value="request('date_to')" /></div>
                <button type="submit" name="period" value="custom" class="btn btn-sm {{ $period==='custom' ? 'btn-primary' : 'btn-outline' }} btn-nb">Terapkan</button>
            </form>
        </div>
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <x-stat-card label="Omzet Periode" value="Rp {{ number_format($periodSales, 0, ',', '.') }}" icon="bi-calendar-check" variant="primary" />
            </div>
            <div class="col-6 col-md-3">
                <x-stat-card label="Laba Periode" value="Rp {{ number_format($periodProfit, 0, ',', '.') }}" icon="bi-graph-up" variant="mint" />
            </div>
            <div class="col-6 col-md-3">
                <x-stat-card label="Margin" value="{{ number_format($periodMargin, 1) }}%" icon="bi-percent" variant="soft" />
            </div>
            <div class="col-6 col-md-3">
                <x-stat-card label="Jumlah Transaksi" value="{{ $periodTransactions }}" icon="bi-receipt-cutoff" variant="white" />
            </div>
        </div>
    </x-card>

    @if(count($insights))
    <x-card title="Business Insight" class="mb-4">
        <ul class="mb-0 ps-3">
            @foreach($insights as $insight)
                <li class="mb-1">{{ $insight }}</li>
            @endforeach
        </ul>
    </x-card>
    @endif

    @if($pendingApprovalTotal > 0 || $pendingDeliveries > 0)
    <x-card title="Menunggu Tindakan" class="mb-4">
        <div class="row g-3">
            @if($pendingApprovals['purchase_orders'] > 0)
            <div class="col-6 col-md-3">
                <a href="{{ route('purchase-orders.index', ['status' => 'submitted']) }}" class="text-decoration-none">
                    <x-stat-card label="PO Perlu Approval" value="{{ $pendingApprovals['purchase_orders'] }}" icon="bi-cart-check" variant="warning" />
                </a>
            </div>
            @endif
            @if($pendingApprovals['stock_opnames'] > 0)
            <div class="col-6 col-md-3">
                <a href="{{ route('stock-opnames.index', ['status' => 'review']) }}" class="text-decoration-none">
                    <x-stat-card label="Opname Perlu Review" value="{{ $pendingApprovals['stock_opnames'] }}" icon="bi-clipboard-data" variant="warning" />
                </a>
            </div>
            @endif
            @if($pendingApprovals['stock_adjustments'] > 0)
            <div class="col-6 col-md-3">
                <a href="{{ route('stock-adjustments.index', ['status' => 'pending']) }}" class="text-decoration-none">
                    <x-stat-card label="Adjustment Pending" value="{{ $pendingApprovals['stock_adjustments'] }}" icon="bi-sliders" variant="warning" />
                </a>
            </div>
            @endif
            @if($pendingDeliveries > 0)
            <div class="col-6 col-md-3">
                <a href="{{ route('delivery-orders.index') }}" class="text-decoration-none">
                    <x-stat-card label="Pengiriman Pending" value="{{ $pendingDeliveries }}" icon="bi-truck" variant="soft" />
                </a>
            </div>
            @endif
        </div>
    </x-card>
    @endif

    <div class="row g-3 mb-3">
        <div class="col-xl-7">
            <x-card title="Penjualan 14 Hari Terakhir" class="h-100">
                <canvas id="salesChart" height="100"></canvas>
            </x-card>
        </div>
        <div class="col-xl-5">
            <x-card title="Metode Pembayaran" subtitle="Periode terpilih" class="h-100">
                @if($paymentBreakdown->isEmpty())
                    <p class="text-muted small mb-0">Belum ada data pembayaran.</p>
                @else
                    <canvas id="paymentChart" height="180"></canvas>
                @endif
            </x-card>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-4">
            <x-card title="Produk Terlaris" subtitle="Periode terpilih" class="h-100">
                @if($topProducts->isEmpty())
                    <p class="text-muted small mb-0">Belum ada penjualan.</p>
                @else
                    @foreach($topProducts as $i => $item)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <div class="d-flex align-items-center gap-2" style="min-width:0;">
                            <span class="badge-nb">{{ $i + 1 }}</span>
                            <div class="text-truncate">
                                <div class="fw-bold small text-truncate">{{ $item->product->name ?? '-' }}</div>
                                <div class="text-muted small">{{ rtrim(rtrim(number_format($item->total_qty,2,'.',''),'0'),'.') }} terjual</div>
                            </div>
                        </div>
                        <span class="small fw-bold flex-shrink-0 ms-2">Rp {{ number_format($item->total_sales,0,',','.') }}</span>
                    </div>
                    @endforeach
                @endif
            </x-card>
        </div>
        <div class="col-lg-4">
            <x-card title="Customer Teratas" subtitle="Periode terpilih" class="h-100">
                @if($topCustomers->isEmpty())
                    <p class="text-muted small mb-0">Belum ada transaksi customer.</p>
                @else
                    @foreach($topCustomers as $i => $row)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <div class="d-flex align-items-center gap-2" style="min-width:0;">
                            <span class="badge-nb">{{ $i + 1 }}</span>
                            <div class="text-truncate">
                                <div class="fw-bold small text-truncate">{{ $row->customer->name ?? '-' }}</div>
                                <div class="text-muted small">{{ $row->trx_count }} transaksi</div>
                            </div>
                        </div>
                        <span class="small fw-bold flex-shrink-0 ms-2">Rp {{ number_format($row->total_spent,0,',','.') }}</span>
                    </div>
                    @endforeach
                @endif
            </x-card>
        </div>
        <div class="col-lg-4">
            <x-card title="Supplier Teratas" subtitle="Periode terpilih" class="h-100">
                @if($topSuppliers->isEmpty())
                    <p class="text-muted small mb-0">Belum ada pembelian.</p>
                @else
                    @foreach($topSuppliers as $i => $row)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <div class="d-flex align-items-center gap-2" style="min-width:0;">
                            <span class="badge-nb">{{ $i + 1 }}</span>
                            <div class="text-truncate">
                                <div class="fw-bold small text-truncate">{{ $row->supplier->name ?? '-' }}</div>
                                <div class="text-muted small">{{ $row->trx_count }} transaksi</div>
                            </div>
                        </div>
                        <span class="small fw-bold flex-shrink-0 ms-2">Rp {{ number_format($row->total_purchased,0,',','.') }}</span>
                    </div>
                    @endforeach
                @endif
            </x-card>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <x-card title="Transaksi Terbaru">
                @if($recentSales->isEmpty())
                    <x-empty-state title="Belum ada transaksi" description="Transaksi penjualan akan muncul di sini." action-label="Buat Transaksi" :action-href="route('pos.index')" />
                @else
                <x-table>
                    <thead><tr><th>Invoice</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($recentSales as $sale)
                        <tr>
                            <td><a href="{{ route('sales.show', $sale) }}">{{ $sale->invoice_number }}</a></td>
                            <td>{{ $sale->customer->name ?? 'Umum' }}</td>
                            <td>Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                            <td><x-badge :variant="$sale->status === 'void' ? 'danger' : 'mint'">{{ ucfirst($sale->status) }}</x-badge></td>
                        </tr>
                        @endforeach
                    </tbody>
                </x-table>
                @endif
            </x-card>

            <x-card title="Penjualan per Kategori" class="mt-3">
                @if($categorySales->isEmpty())
                    <p class="text-muted small mb-0">Belum ada data kategori.</p>
                @else
                    <canvas id="categoryChart" height="160"></canvas>
                @endif
            </x-card>
        </div>

        <div class="col-lg-5">
            <x-card title="Stok Kritis &amp; Habis">
                @if($lowStockProducts->isEmpty())
                    <p class="text-muted small mb-0">Semua stok dalam kondisi aman.</p>
                @else
                    @foreach($lowStockProducts as $p)
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <div class="text-truncate" style="min-width:0;">
                            <div class="fw-bold small text-truncate">{{ $p->name }}</div>
                            <div class="text-muted small">Min: {{ $p->minimum_stock }}</div>
                        </div>
                        <x-badge :variant="$p->totalStock() <= 0 ? 'danger' : 'warning'">{{ rtrim(rtrim(number_format($p->totalStock(),2,'.',''),'0'),'.') }}</x-badge>
                    </div>
                    @endforeach
                @endif
            </x-card>

            <x-card title="Piutang Overdue" class="mt-3">
                @if($overdueReceivables->isEmpty())
                    <p class="text-muted small mb-0">Tidak ada piutang jatuh tempo.</p>
                @else
                    @foreach($overdueReceivables as $s)
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <div class="text-truncate" style="min-width:0;">
                            <div class="fw-bold small text-truncate">{{ $s->customer->name ?? '-' }}</div>
                            <div class="text-muted small">{{ $s->invoice_number }}</div>
                        </div>
                        <span class="small fw-bold text-danger flex-shrink-0 ms-2">Rp {{ number_format($s->outstanding(), 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                @endif
            </x-card>
        </div>
    </div>

    @push('scripts')
    <script>
        new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($salesByDay->keys()) !!},
                datasets: [{
                    label: 'Penjualan',
                    data: {!! json_encode($salesByDay->values()) !!},
                    borderColor: '#1E3A5F',
                    backgroundColor: 'rgba(191,215,255,0.4)',
                    fill: true,
                    tension: 0.3,
                }]
            },
            options: { plugins: { legend: { display: false } }, maintainAspectRatio: true }
        });

        @if($paymentBreakdown->isNotEmpty())
        new Chart(document.getElementById('paymentChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($paymentBreakdown->keys()) !!},
                datasets: [{
                    data: {!! json_encode($paymentBreakdown->values()) !!},
                    backgroundColor: ['#BFD7FF', '#C8F0E0', '#FFE7A8', '#FFC7C7', '#DCEBFF', '#1E3A5F'],
                    borderColor: '#172033',
                    borderWidth: 2,
                }]
            },
            options: { plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } } }
        });
        @endif

        @if($categorySales->isNotEmpty())
        new Chart(document.getElementById('categoryChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($categorySales->keys()) !!},
                datasets: [{
                    label: 'Penjualan',
                    data: {!! json_encode($categorySales->values()) !!},
                    backgroundColor: '#BFD7FF',
                    borderColor: '#172033',
                    borderWidth: 2,
                }]
            },
            options: { plugins: { legend: { display: false } }, indexAxis: 'y' }
        });
        @endif
    </script>
    @endpush
</x-app-layout>
