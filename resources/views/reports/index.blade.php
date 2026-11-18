<x-app-layout title="Laporan">
    <div class="row g-3">
        @php
        $reports = [
            ['reports.sales', 'bi-receipt', 'Sales Report', 'Laporan penjualan periode tertentu'],
            ['reports.purchases', 'bi-cart-check', 'Purchase Report', 'Laporan pembelian dari supplier'],
            ['reports.products', 'bi-box-seam', 'Product Sales', 'Penjualan per produk'],
            ['reports.stock', 'bi-boxes', 'Stock Report', 'Nilai & posisi stok saat ini'],
            ['reports.stock-opname', 'bi-clipboard-data', 'Stock Opname', 'Riwayat stock opname'],
            ['customers.receivables', 'bi-wallet2', 'Receivable', 'Piutang customer & aging'],
            ['suppliers.payables', 'bi-credit-card', 'Payable', 'Hutang supplier & aging'],
            ['reports.cash-flow', 'bi-arrow-down-up', 'Cash Flow', 'Arus kas masuk & keluar'],
            ['reports.profit-loss', 'bi-graph-up', 'Laba Rugi', 'Ringkasan profit & loss'],
            ['reports.profitability', 'bi-percent', 'Profitability', 'Margin keuntungan per produk'],
            ['reports.customers', 'bi-people', 'Customer Report', 'Ranking pembelian customer'],
            ['reports.suppliers', 'bi-building', 'Supplier Report', 'Ranking pembelian supplier'],
            ['reports.projects', 'bi-kanban', 'Project Report', 'Performa transaksi per project'],
            ['reports.deliveries', 'bi-truck', 'Delivery Report', 'Status pengiriman'],
        ];
        @endphp
        @foreach($reports as [$route, $icon, $title, $desc])
        <div class="col-md-4 col-lg-3">
            <a href="{{ route($route) }}" class="text-decoration-none">
                <div class="nb-card-flat p-3 h-100">
                    <i class="bi {{ $icon }}" style="font-size:1.6rem;color:var(--color-deep);"></i>
                    <h6 class="mt-2 mb-1">{{ $title }}</h6>
                    <p class="small text-muted mb-0">{{ $desc }}</p>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</x-app-layout>
