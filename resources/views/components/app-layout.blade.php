@props(['title' => 'Dashboard'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} — {{ config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('bangunan.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="app-shell">
    <aside class="app-sidebar" id="appSidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('bangunan.png') }}" alt="Logo" class="brand-mark" style="object-fit:contain;background:#fff;">
            <div>
                <div>TOKO BANGUNAN</div>
                <div style="font-size:.65rem;font-weight:600;color:#6b7a93;">POS &amp; ERP System</div>
            </div>
        </div>

        <nav class="pb-4">
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>

            <div class="sidebar-section">Transaksi</div>
            @can('sales.create')<a href="{{ route('pos.index') }}" class="sidebar-link {{ request()->routeIs('pos.*') ? 'active' : '' }}"><i class="bi bi-cash-coin"></i> POS Kasir</a>@endcan
            @can('quotation.view')<a href="{{ route('quotations.index') }}" class="sidebar-link {{ request()->routeIs('quotations.*') ? 'active' : '' }}"><i class="bi bi-file-earmark-text"></i> Quotation</a>@endcan
            @can('sales_order.view')<a href="{{ route('sales-orders.index') }}" class="sidebar-link {{ request()->routeIs('sales-orders.*') ? 'active' : '' }}"><i class="bi bi-clipboard-check"></i> Sales Order</a>@endcan
            @can('sales.view')<a href="{{ route('sales.index') }}" class="sidebar-link {{ request()->routeIs('sales.*') ? 'active' : '' }}"><i class="bi bi-receipt"></i> Penjualan</a>@endcan
            @can('sales_return.view')<a href="{{ route('sale-returns.index') }}" class="sidebar-link {{ request()->routeIs('sale-returns.*') ? 'active' : '' }}"><i class="bi bi-arrow-return-left"></i> Retur Penjualan</a>@endcan
            @can('delivery.view')<a href="{{ route('delivery-orders.index') }}" class="sidebar-link {{ request()->routeIs('delivery-orders.*') ? 'active' : '' }}"><i class="bi bi-truck"></i> Delivery Order</a>@endcan

            <div class="sidebar-section">Pembelian</div>
            @can('purchase_order.view')<a href="{{ route('purchase-orders.index') }}" class="sidebar-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}"><i class="bi bi-cart-plus"></i> Purchase Order</a>@endcan
            @can('goods_receipt.view')<a href="{{ route('goods-receipts.index') }}" class="sidebar-link {{ request()->routeIs('goods-receipts.*') ? 'active' : '' }}"><i class="bi bi-box-seam"></i> Penerimaan Barang</a>@endcan
            @can('purchase.view')<a href="{{ route('purchases.index') }}" class="sidebar-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}"><i class="bi bi-bag-check"></i> Pembelian</a>@endcan
            @can('purchase_return.view')<a href="{{ route('purchase-returns.index') }}" class="sidebar-link {{ request()->routeIs('purchase-returns.*') ? 'active' : '' }}"><i class="bi bi-arrow-counterclockwise"></i> Retur Pembelian</a>@endcan
            @can('payment.supplier')<a href="{{ route('suppliers.payables') }}" class="sidebar-link {{ request()->routeIs('suppliers.payables') ? 'active' : '' }}"><i class="bi bi-credit-card"></i> Pembayaran Supplier</a>@endcan

            <div class="sidebar-section">Inventory</div>
            @can('product.view')<a href="{{ route('products.index') }}" class="sidebar-link {{ request()->routeIs('products.*') ? 'active' : '' }}"><i class="bi bi-box"></i> Produk</a>@endcan
            @can('category.view')<a href="{{ route('categories.index') }}" class="sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}"><i class="bi bi-diagram-3"></i> Kategori</a>@endcan
            @can('category.view')<a href="{{ route('brands.index') }}" class="sidebar-link {{ request()->routeIs('brands.*') ? 'active' : '' }}"><i class="bi bi-award"></i> Brand</a>@endcan
            @can('settings.manage')<a href="{{ route('units.index') }}" class="sidebar-link {{ request()->routeIs('units.*') ? 'active' : '' }}"><i class="bi bi-rulers"></i> Satuan</a>@endcan
            @can('settings.manage')<a href="{{ route('warehouses.index') }}" class="sidebar-link {{ request()->routeIs('warehouses.*') ? 'active' : '' }}"><i class="bi bi-building"></i> Gudang</a>@endcan
            @can('stock.view')<a href="{{ route('stocks.index') }}" class="sidebar-link {{ request()->routeIs('stocks.index') ? 'active' : '' }}"><i class="bi bi-boxes"></i> Stok</a>@endcan
            @can('stock.view')<a href="{{ route('stocks.card') }}" class="sidebar-link {{ request()->routeIs('stocks.card') ? 'active' : '' }}"><i class="bi bi-journal-text"></i> Stock Card</a>@endcan
            @can('stock.transfer')<a href="{{ route('stock-transfers.index') }}" class="sidebar-link {{ request()->routeIs('stock-transfers.*') ? 'active' : '' }}"><i class="bi bi-arrow-left-right"></i> Stock Transfer</a>@endcan
            @can('stock.opname')<a href="{{ route('stock-opnames.index') }}" class="sidebar-link {{ request()->routeIs('stock-opnames.*') ? 'active' : '' }}"><i class="bi bi-clipboard-data"></i> Stock Opname</a>@endcan
            @can('stock.adjustment')<a href="{{ route('stock-adjustments.index') }}" class="sidebar-link {{ request()->routeIs('stock-adjustments.*') ? 'active' : '' }}"><i class="bi bi-sliders"></i> Stock Adjustment</a>@endcan
            @can('product.view')<a href="{{ route('products.barcode') }}" class="sidebar-link {{ request()->routeIs('products.barcode') ? 'active' : '' }}"><i class="bi bi-upc-scan"></i> Barcode</a>@endcan

            <div class="sidebar-section">Customer</div>
            @can('customer.view')<a href="{{ route('customers.index') }}" class="sidebar-link {{ request()->routeIs('customers.*') && !request()->routeIs('customers.receivables') ? 'active' : '' }}"><i class="bi bi-people"></i> Customer</a>@endcan
            @can('payment.receive')<a href="{{ route('customers.receivables') }}" class="sidebar-link {{ request()->routeIs('customers.receivables') ? 'active' : '' }}"><i class="bi bi-wallet2"></i> Piutang</a>@endcan
            @can('project.view')<a href="{{ route('customer-projects.index') }}" class="sidebar-link {{ request()->routeIs('customer-projects.*') ? 'active' : '' }}"><i class="bi bi-kanban"></i> Project</a>@endcan

            <div class="sidebar-section">Supplier</div>
            @can('supplier.view')<a href="{{ route('suppliers.index') }}" class="sidebar-link {{ request()->routeIs('suppliers.index') || request()->routeIs('suppliers.show') ? 'active' : '' }}"><i class="bi bi-building-gear"></i> Supplier</a>@endcan

            <div class="sidebar-section">Logistik</div>
            @can('delivery.view')<a href="{{ route('drivers.index') }}" class="sidebar-link {{ request()->routeIs('drivers.*') ? 'active' : '' }}"><i class="bi bi-person-badge"></i> Driver</a>@endcan
            @can('delivery.view')<a href="{{ route('vehicles.index') }}" class="sidebar-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}"><i class="bi bi-truck-front"></i> Kendaraan</a>@endcan

            <div class="sidebar-section">Keuangan</div>
            @can('cash.view')<a href="{{ route('cash-accounts.index') }}" class="sidebar-link {{ request()->routeIs('cash-accounts.*') ? 'active' : '' }}"><i class="bi bi-bank"></i> Kas</a>@endcan
            @can('cashier_shift.view')<a href="{{ route('cashier-shifts.index') }}" class="sidebar-link {{ request()->routeIs('cashier-shifts.*') ? 'active' : '' }}"><i class="bi bi-person-workspace"></i> Kasir Shift</a>@endcan
            @can('cash.create')<a href="{{ route('cash-transactions.index') }}" class="sidebar-link {{ request()->routeIs('cash-transactions.*') ? 'active' : '' }}"><i class="bi bi-arrow-down-up"></i> Mutasi Kas</a>@endcan
            @can('expense.view')<a href="{{ route('expenses.index') }}" class="sidebar-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}"><i class="bi bi-cash-stack"></i> Pengeluaran</a>@endcan

            <div class="sidebar-section">Harga</div>
            @can('product.edit')<a href="{{ route('price-levels.index') }}" class="sidebar-link {{ request()->routeIs('price-levels.*') ? 'active' : '' }}"><i class="bi bi-tags"></i> Price List</a>@endcan

            <div class="sidebar-section">Laporan</div>
            @can('report.view')<a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"><i class="bi bi-bar-chart-line"></i> Laporan</a>@endcan

            <div class="sidebar-section">Master</div>
            @can('settings.manage')<a href="{{ route('taxes.index') }}" class="sidebar-link {{ request()->routeIs('taxes.*') ? 'active' : '' }}"><i class="bi bi-percent"></i> Pajak</a>@endcan
            @can('settings.manage')<a href="{{ route('payment-methods.index') }}" class="sidebar-link {{ request()->routeIs('payment-methods.*') ? 'active' : '' }}"><i class="bi bi-credit-card-2-front"></i> Payment Method</a>@endcan
            @can('settings.manage')<a href="{{ route('settings.edit') }}" class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}"><i class="bi bi-gear"></i> Settings</a>@endcan

            <div class="sidebar-section">User Management</div>
            @can('user.manage')<a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}"><i class="bi bi-person-lines-fill"></i> Users</a>@endcan
            @can('role.manage')<a href="{{ route('roles.index') }}" class="sidebar-link {{ request()->routeIs('roles.*') ? 'active' : '' }}"><i class="bi bi-shield-lock"></i> Roles</a>@endcan
            @can('audit.view')<a href="{{ route('audit-logs.index') }}" class="sidebar-link {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}"><i class="bi bi-file-earmark-lock"></i> Audit Log</a>@endcan
        </nav>
    </aside>

    <div class="app-main">
        <header class="app-topbar">
            <div class="d-flex align-items-center gap-2 gap-sm-3" style="min-width:0;">
                <button class="btn btn-outline btn-nb d-lg-none flex-shrink-0" type="button" onclick="document.getElementById('appSidebar').classList.toggle('show')">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="mb-0 text-truncate">{{ $title ?? 'Dashboard' }}</h5>
            </div>
            <div class="d-flex align-items-center gap-2 gap-sm-3 flex-shrink-0">
                @if($activeShift ?? null)
                    <span class="badge-nb d-none d-sm-inline-flex"><i class="bi bi-circle-fill text-success" style="font-size:.55rem;"></i> Shift Aktif</span>
                    <span class="badge-nb d-sm-none" title="Shift Aktif"><i class="bi bi-circle-fill text-success" style="font-size:.55rem;"></i></span>
                @endif
                <div class="dropdown">
                    <button class="btn btn-outline btn-nb dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text small fw-bold">{{ auth()->user()->name }}</span></li>
                        <li><span class="dropdown-item-text small text-muted">{{ auth()->user()->getRoleNames()->first() }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="app-content">
            @if (session('success'))
                <x-alert type="mint" :dismissible="true" class="mb-3">{{ session('success') }}</x-alert>
            @endif
            @if (session('error'))
                <x-alert type="danger" :dismissible="true" class="mb-3">{{ session('error') }}</x-alert>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
