<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POS Kasir — {{ config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div x-data="posApp()" x-init="init()" @keydown.window="handleKey($event)">

    <header class="app-topbar" style="position:sticky;top:0;z-index:1020;">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('dashboard') }}" class="btn btn-outline btn-nb btn-sm"><i class="bi bi-arrow-left"></i></a>
            <h5 class="mb-0">POS Kasir</h5>
            <span class="badge-nb"><i class="bi bi-person-workspace"></i> {{ auth()->user()->name }}</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="kbd-hint">F2 Cari</span>
            <span class="kbd-hint">F4 Customer</span>
            <span class="kbd-hint">F8 Bayar</span>
            <span class="kbd-hint">F9 Hold</span>
            <button class="btn btn-outline btn-nb btn-sm" @click="showHeld = true"><i class="bi bi-pause-circle"></i> Held (<span x-text="heldSales.length"></span>)</button>
            <a href="{{ route('cashier-shifts.close-form') }}" class="btn btn-outline btn-nb btn-sm"><i class="bi bi-box-arrow-right"></i> Tutup Shift</a>
        </div>
    </header>

    <div class="app-content pos-grid">
        <!-- LEFT: search + products -->
        <div class="d-flex flex-column" style="min-height:0;">
            <div class="row g-2 mb-2">
                <div class="col-md-6">
                    <input type="text" x-ref="searchInput" x-model="search" @input.debounce.300ms="loadProducts()" @keydown.enter="tryBarcodeAdd()" class="form-control" placeholder="Cari produk / scan barcode... (F2)">
                </div>
                <div class="col-md-3">
                    <select class="form-select" x-model="warehouseId" @change="loadProducts()">
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}" @selected($w->id === ($defaultWarehouse->id ?? null))>{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" x-model="categoryId" @change="loadProducts()">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex-grow-1" style="overflow-y:auto;">
                <div class="row g-2" x-show="!loading">
                    <template x-for="p in products" :key="p.id">
                        <div class="col-6 col-md-4 col-xl-3">
                            <div class="pos-product-card" @click="addProduct(p)">
                                <div class="fw-bold small" x-text="p.name"></div>
                                <div class="text-muted small" x-text="p.sku"></div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="small fw-bold">Rp <span x-text="fmt(p.units[0]?.price || 0)"></span></span>
                                    <span class="badge-nb" :class="p.stock <= 0 ? 'text-danger' : ''" x-text="p.stock"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div x-show="products.length === 0" class="col-12 text-center text-muted py-5">Tidak ada produk ditemukan.</div>
                </div>
                <div x-show="loading" class="text-center text-muted py-5">Memuat...</div>
            </div>
        </div>

        <!-- RIGHT: cart -->
        <div class="pos-cart-panel">
            <div class="nb-panel-header">
                <div>
                    <div class="fw-bold" x-text="customer ? customer.name : 'Customer Umum'"></div>
                    <div class="small text-muted" x-text="priceLevelLabel"></div>
                </div>
                <button class="btn btn-sm btn-outline btn-nb" @click="showCustomerModal = true"><i class="bi bi-person"></i> Pilih</button>
            </div>

            <div class="flex-grow-1 p-3" style="overflow-y:auto;">
                <template x-if="cart.length === 0">
                    <div class="text-center text-muted py-5"><i class="bi bi-cart display-6"></i><p class="mt-2 small">Keranjang kosong</p></div>
                </template>
                <template x-for="(item, idx) in cart" :key="idx">
                    <div class="mb-2 pb-2" style="border-bottom:1px solid var(--color-gray);">
                        <div class="d-flex justify-content-between">
                            <span class="small fw-bold" x-text="item.name"></span>
                            <button class="btn btn-sm text-danger border-0 bg-transparent p-0" @click="cart.splice(idx,1)"><i class="bi bi-x-lg"></i></button>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <select class="form-select form-select-sm" style="width:auto;" x-model.number="item.unit_id" @change="onUnitChange(item)">
                                <template x-for="u in item.availableUnits" :key="u.unit_id">
                                    <option :value="u.unit_id" x-text="u.code"></option>
                                </template>
                            </select>
                            <input type="number" step="0.001" min="0.001" class="form-control form-control-sm" style="width:70px;" x-model.number="item.qty" @input="recalc()">
                            <input type="number" step="1" min="0" class="form-control form-control-sm" style="width:100px;" x-model.number="item.price" @input="recalc()">
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <input type="number" step="1" min="0" class="form-control form-control-sm" style="width:100px;" x-model.number="item.discount_amount" @input="recalc()" placeholder="Diskon">
                            <span class="small fw-bold">Rp <span x-text="fmt(item.qty * item.price - (item.discount_amount||0))"></span></span>
                        </div>
                    </div>
                </template>
            </div>

            <div class="p-3" style="border-top:3px solid var(--color-border);">
                <div class="d-flex justify-content-between small mb-1"><span>Subtotal</span><span x-text="'Rp ' + fmt(subtotal)"></span></div>
                <div class="d-flex justify-content-between small mb-1 align-items-center">
                    <span>Diskon Transaksi</span>
                    <input type="number" step="1" min="0" class="form-control form-control-sm" style="width:110px;" x-model.number="discountAmount" @input="recalc()">
                </div>
                <div class="pos-total-box mt-2 mb-2">
                    <div class="small" style="opacity:.8;">TOTAL BAYAR</div>
                    <div class="amount">Rp <span x-text="fmt(total)"></span></div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline btn-nb flex-grow-1" @click="holdCart()"><i class="bi bi-pause-circle"></i> Hold (F9)</button>
                    <button class="pay-btn" @click="showPaymentModal = true" :disabled="cart.length===0">BAYAR SEKARANG (F8)</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Modal -->
    <div class="modal fade" :class="{show: showCustomerModal}" :style="showCustomerModal ? 'display:block;background:rgba(0,0,0,.4);' : ''" @keydown.escape.window="showCustomerModal=false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border:3px solid var(--color-border);border-radius:8px;box-shadow:5px 5px 0 var(--color-border);">
                <div class="modal-header" style="border-bottom:3px solid var(--color-border);">
                    <h5 class="modal-title">Pilih Customer</h5>
                    <button class="btn-close" @click="showCustomerModal=false"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control mb-3" placeholder="Cari customer..." x-model="customerSearch" @input.debounce.300ms="loadCustomers()">
                    <div style="max-height:300px;overflow-y:auto;">
                        <div class="p-2 mb-1 nb-card-flat" style="cursor:pointer;" @click="selectCustomer(null)">Customer Umum (Default)</div>
                        <template x-for="c in customers" :key="c.id">
                            <div class="p-2 mb-1 nb-card-flat" style="cursor:pointer;" @click="selectCustomer(c)">
                                <div class="fw-bold small" x-text="c.name"></div>
                                <div class="small text-muted" x-text="c.customer_type"></div>
                            </div>
                        </template>
                    </div>
                    <hr>
                    <label class="form-label">Price Level (untuk customer umum)</label>
                    <select class="form-select" x-model="priceLevelId" @change="setPriceLevel()">
                        <option value="">Default</option>
                        @foreach($priceLevels as $pl)
                            <option value="{{ $pl->id }}">{{ $pl->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" :class="{show: showPaymentModal}" :style="showPaymentModal ? 'display:block;background:rgba(0,0,0,.4);' : ''">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border:3px solid var(--color-border);border-radius:8px;box-shadow:5px 5px 0 var(--color-border);">
                <div class="modal-header" style="border-bottom:3px solid var(--color-border);">
                    <h5 class="modal-title">Pembayaran</h5>
                    <button class="btn-close" @click="showPaymentModal=false"></button>
                </div>
                <div class="modal-body">
                    <div class="pos-total-box mb-3"><div class="small" style="opacity:.8;">TOTAL</div><div class="amount">Rp <span x-text="fmt(total)"></span></div></div>

                    <template x-for="(p, idx) in payments" :key="idx">
                        <div class="d-flex gap-2 mb-2">
                            <select class="form-select" x-model.number="p.payment_method_id">
                                @foreach($paymentMethods as $pm)
                                    <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                                @endforeach
                            </select>
                            <input type="number" step="1" class="form-control" x-model.number="p.amount" @input="recalcPayment()">
                            <button class="btn btn-sm btn-danger btn-nb" @click="payments.splice(idx,1); recalcPayment()"><i class="bi bi-trash"></i></button>
                        </div>
                    </template>
                    <button class="btn btn-sm btn-outline btn-nb mb-3" @click="payments.push({payment_method_id: {{ $paymentMethods->first()->id ?? 1 }}, amount: 0})"><i class="bi bi-plus"></i> Split Pembayaran</button>

                    <div class="d-flex justify-content-between mb-1"><span>Total Dibayar</span><strong x-text="'Rp ' + fmt(totalPaid)"></strong></div>
                    <div class="d-flex justify-content-between mb-1" x-show="totalPaid >= total"><span>Kembalian</span><strong x-text="'Rp ' + fmt(Math.max(0,totalPaid-total))"></strong></div>
                    <div class="d-flex justify-content-between mb-3 text-danger" x-show="totalPaid < total"><span>Kurang (menjadi piutang)</span><strong x-text="'Rp ' + fmt(total-totalPaid)"></strong></div>

                    <button class="pay-btn" @click="submitCheckout()" :disabled="submitting">
                        <span x-show="!submitting">Konfirmasi &amp; Simpan</span>
                        <span x-show="submitting">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Held sales -->
    <div class="modal fade" :class="{show: showHeld}" :style="showHeld ? 'display:block;background:rgba(0,0,0,.4);' : ''">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border:3px solid var(--color-border);border-radius:8px;box-shadow:5px 5px 0 var(--color-border);">
                <div class="modal-header" style="border-bottom:3px solid var(--color-border);">
                    <h5 class="modal-title">Transaksi Ditahan (Hold)</h5>
                    <button class="btn-close" @click="showHeld=false"></button>
                </div>
                <div class="modal-body" style="max-height:400px;overflow-y:auto;">
                    <template x-for="s in heldSales" :key="s.id">
                        <div class="p-2 mb-1 nb-card-flat d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold small" x-text="s.invoice_number"></div>
                                <div class="small text-muted" x-text="(s.customer ? s.customer.name : 'Umum') + ' — Rp ' + fmt(s.total)"></div>
                            </div>
                            <button class="btn btn-sm btn-primary btn-nb" @click="resumeSale(s)">Lanjutkan</button>
                        </div>
                    </template>
                    <div x-show="heldSales.length===0" class="text-muted small text-center py-3">Tidak ada transaksi ditahan.</div>
                </div>
            </div>
        </div>
    </div>

    <form :action="'{{ route('pos.checkout') }}'" method="POST" x-ref="checkoutForm" style="display:none;">
        @csrf
        <input type="hidden" name="payload" x-ref="payloadInput">
    </form>
</div>

<script>
function posApp() {
    return {
        search: '', categoryId: '', warehouseId: '{{ $defaultWarehouse->id ?? '' }}',
        products: [], loading: false,
        cart: [], discountAmount: 0, subtotal: 0, total: 0,
        customer: null, customerSearch: '', customers: [],
        priceLevelId: '', priceLevelLabel: 'Price Level: Default',
        showCustomerModal: false, showPaymentModal: false, showHeld: false,
        payments: [], totalPaid: 0, submitting: false,
        heldSales: @js($heldSales),
        resumeSaleId: null,

        init() {
            this.loadProducts();
            this.$refs.searchInput.focus();
        },
        fmt(n) { return new Intl.NumberFormat('id-ID').format(Math.round(n || 0)); },
        async loadProducts() {
            this.loading = true;
            const params = new URLSearchParams({search: this.search, category_id: this.categoryId, warehouse_id: this.warehouseId, customer_id: this.customer?.id || '', price_level_id: this.priceLevelId});
            const res = await fetch('{{ route('pos.products') }}?' + params.toString());
            this.products = await res.json();
            this.loading = false;
        },
        tryBarcodeAdd() {
            const exact = this.products.find(p => p.barcode === this.search || p.sku === this.search);
            if (exact) { this.addProduct(exact); this.search=''; this.loadProducts(); }
        },
        addProduct(p) {
            if (!p.units.length) return;
            const existing = this.cart.find(c => c.product_id === p.id && c.unit_id === p.units[0].unit_id);
            if (existing) { existing.qty++; this.recalc(); return; }
            this.cart.push({
                product_id: p.id, name: p.name, unit_id: p.units[0].unit_id,
                availableUnits: p.units, qty: 1, price: p.units[0].price, discount_amount: 0,
            });
            this.recalc();
        },
        onUnitChange(item) {
            const u = item.availableUnits.find(u => u.unit_id === item.unit_id);
            if (u) item.price = u.price;
            this.recalc();
        },
        recalc() {
            this.subtotal = this.cart.reduce((s, i) => s + (i.qty * i.price - (i.discount_amount || 0)), 0);
            this.total = Math.max(0, this.subtotal - (this.discountAmount || 0));
        },
        recalcPayment() {
            this.totalPaid = this.payments.reduce((s, p) => s + (Number(p.amount) || 0), 0);
        },
        async loadCustomers() {
            const res = await fetch('{{ route('pos.customers') }}?search=' + encodeURIComponent(this.customerSearch));
            try { this.customers = await res.json(); } catch(e) { this.customers = []; }
        },
        selectCustomer(c) {
            this.customer = c;
            this.showCustomerModal = false;
            this.loadProducts();
        },
        setPriceLevel() {
            this.priceLevelLabel = this.priceLevelId ? 'Price Level dipilih' : 'Price Level: Default';
            this.loadProducts();
        },
        handleKey(e) {
            if (e.key === 'F2') { e.preventDefault(); this.$refs.searchInput.focus(); }
            if (e.key === 'F4') { e.preventDefault(); this.showCustomerModal = true; }
            if (e.key === 'F8') { e.preventDefault(); if (this.cart.length) this.showPaymentModal = true; }
            if (e.key === 'F9') { e.preventDefault(); this.holdCart(); }
            if (e.key === 'Escape') { this.showCustomerModal = false; this.showPaymentModal = false; this.showHeld = false; }
        },
        buildPayload() {
            return {
                warehouse_id: this.warehouseId, customer_id: this.customer?.id || null,
                price_level_id: this.priceLevelId || null, discount_amount: this.discountAmount || 0,
                resume_sale_id: this.resumeSaleId,
                items: this.cart.map(i => ({product_id: i.product_id, unit_id: i.unit_id, qty: i.qty, price: i.price, discount_amount: i.discount_amount || 0})),
                payments: this.payments,
            };
        },
        async submitCheckout() {
            this.submitting = true;
            const payload = this.buildPayload();
            const res = await fetch('{{ route('pos.checkout') }}', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json'},
                body: JSON.stringify(payload),
            });
            if (res.redirected) { window.location.href = res.url; return; }
            const data = await res.json().catch(() => ({}));
            this.submitting = false;
            alert(data.message || 'Terjadi kesalahan saat menyimpan transaksi.');
        },
        async holdCart() {
            if (!this.cart.length) return;
            const payload = this.buildPayload();
            delete payload.payments;
            const res = await fetch('{{ route('pos.hold') }}', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json'},
                body: JSON.stringify(payload),
            });
            if (res.redirected) { window.location.href = res.url; return; }
        },
        resumeSale(s) {
            this.cart = s.items.map(i => ({
                product_id: i.product_id, name: i.product.name, unit_id: i.unit_id,
                availableUnits: [{unit_id: i.unit_id, code: i.unit.code, price: i.price}],
                qty: Number(i.qty), price: Number(i.price), discount_amount: Number(i.discount_amount),
            }));
            this.customer = s.customer;
            this.resumeSaleId = s.id;
            this.showHeld = false;
            this.recalc();
        },
    };
}
</script>
</body>
</html>
