<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CashAccountController;
use App\Http\Controllers\CashierShiftController;
use App\Http\Controllers\CashTransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerProjectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryOrderController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GoodsReceiptController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\PriceLevelController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleReturnController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // POS
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [PosController::class, 'index'])->name('index');
        Route::get('/products', [PosController::class, 'products'])->name('products');
        Route::get('/customers', [PosController::class, 'customers'])->name('customers');
        Route::post('/checkout', [PosController::class, 'checkout'])->name('checkout');
        Route::post('/hold', [PosController::class, 'hold'])->name('hold');
        Route::get('/held', [PosController::class, 'held'])->name('held');
        Route::post('/void/{sale}', [PosController::class, 'void'])->name('void')->middleware('permission:sales.void');
    });

    Route::resource('quotations', QuotationController::class);
    Route::post('quotations/{quotation}/convert', [QuotationController::class, 'convert'])->name('quotations.convert');
    Route::post('quotations/{quotation}/send', [QuotationController::class, 'send'])->name('quotations.send');
    Route::post('quotations/{quotation}/approve', [QuotationController::class, 'approve'])->name('quotations.approve');
    Route::post('quotations/{quotation}/reject', [QuotationController::class, 'reject'])->name('quotations.reject');
    Route::get('quotations/{quotation}/print', [QuotationController::class, 'print'])->name('quotations.print');

    Route::resource('sales-orders', SalesOrderController::class)->except(['edit', 'update', 'destroy']);
    Route::get('sales-orders/{sales_order}/fulfill', [SalesOrderController::class, 'fulfillForm'])->name('sales-orders.fulfill-form');
    Route::post('sales-orders/{sales_order}/fulfill', [SalesOrderController::class, 'fulfill'])->name('sales-orders.fulfill');

    Route::resource('sales', SaleController::class)->only(['index', 'show']);
    Route::get('sales/{sale}/print', [SaleController::class, 'print'])->name('sales.print');
    Route::get('sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');

    Route::resource('sale-returns', SaleReturnController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('sales/{sale}/returnable-items', [SaleReturnController::class, 'returnableItems'])->name('sales.returnable-items');

    Route::resource('delivery-orders', DeliveryOrderController::class);
    Route::get('delivery-orders/{delivery_order}/print', [DeliveryOrderController::class, 'print'])->name('delivery-orders.print');

    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::post('purchase-orders/{purchase_order}/submit', [PurchaseOrderController::class, 'submit'])->name('purchase-orders.submit');
    Route::post('purchase-orders/{purchase_order}/approve', [PurchaseOrderController::class, 'approve'])->name('purchase-orders.approve')->middleware('permission:purchase_order.approve');
    Route::get('purchase-orders/{purchase_order}/print', [PurchaseOrderController::class, 'print'])->name('purchase-orders.print');

    Route::resource('goods-receipts', GoodsReceiptController::class)->only(['index', 'create', 'store', 'show']);

    Route::resource('purchases', PurchaseController::class)->only(['index', 'show']);
    Route::post('purchases/{purchase}/pay', [PurchaseController::class, 'pay'])->name('purchases.pay');

    Route::resource('purchase-returns', PurchaseReturnController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('purchases/{purchase}/returnable-items', [PurchaseReturnController::class, 'returnableItems'])->name('purchases.returnable-items');

    Route::resource('products', ProductController::class);
    Route::get('products-barcode', [ProductController::class, 'barcode'])->name('products.barcode');
    Route::get('products/{product}/prices', [ProductController::class, 'prices'])->name('products.prices');
    Route::post('products/{product}/prices', [ProductController::class, 'storePrice'])->name('products.prices.store');
    Route::delete('product-prices/{productPrice}', [ProductController::class, 'destroyPrice'])->name('products.prices.destroy');

    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('brands', BrandController::class)->except(['show']);

    Route::get('stocks', [StockController::class, 'index'])->name('stocks.index');
    Route::get('stock-card', [StockController::class, 'card'])->name('stocks.card');

    Route::resource('stock-transfers', StockTransferController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('stock-transfers/{stock_transfer}/approve', [StockTransferController::class, 'approve'])->name('stock-transfers.approve');
    Route::post('stock-transfers/{stock_transfer}/ship', [StockTransferController::class, 'ship'])->name('stock-transfers.ship');
    Route::post('stock-transfers/{stock_transfer}/receive', [StockTransferController::class, 'receive'])->name('stock-transfers.receive');

    Route::resource('stock-opnames', StockOpnameController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('stock-opnames/{stock_opname}/count', [StockOpnameController::class, 'saveCount'])->name('stock-opnames.count');
    Route::post('stock-opnames/{stock_opname}/submit-review', [StockOpnameController::class, 'submitReview'])->name('stock-opnames.submit-review');
    Route::post('stock-opnames/{stock_opname}/approve', [StockOpnameController::class, 'approve'])->name('stock-opnames.approve')->middleware('permission:stock.opname.approve');
    Route::post('stock-opnames/{stock_opname}/post', [StockOpnameController::class, 'post'])->name('stock-opnames.post')->middleware('permission:stock.opname.approve');
    Route::get('stock-opnames/{stock_opname}/print', [StockOpnameController::class, 'print'])->name('stock-opnames.print');

    Route::resource('stock-adjustments', StockAdjustmentController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('stock-adjustments/{stock_adjustment}/approve', [StockAdjustmentController::class, 'approve'])->name('stock-adjustments.approve')->middleware('permission:stock.opname.approve');

    Route::resource('customers', CustomerController::class);
    Route::get('customers-receivables', [CustomerController::class, 'receivables'])->name('customers.receivables');
    Route::post('sales/{sale}/receive-payment', [CustomerController::class, 'receivePayment'])->name('customers.receive-payment');
    Route::post('customers/{customer}/special-prices', [CustomerController::class, 'storeSpecialPrice'])->name('customers.special-prices.store');
    Route::delete('customer-prices/{customerPrice}', [CustomerController::class, 'destroySpecialPrice'])->name('customers.special-prices.destroy');

    Route::resource('customer-projects', CustomerProjectController::class);

    Route::resource('suppliers', SupplierController::class);
    Route::get('suppliers-payables', [SupplierController::class, 'payables'])->name('suppliers.payables');

    Route::resource('drivers', DriverController::class)->except(['show']);
    Route::resource('vehicles', VehicleController::class)->except(['show']);

    Route::resource('cash-accounts', CashAccountController::class)->except(['show']);
    Route::resource('cashier-shifts', CashierShiftController::class)->only(['index', 'show']);
    Route::get('cashier-shift/open', [CashierShiftController::class, 'openForm'])->name('cashier-shifts.open-form');
    Route::post('cashier-shift/open', [CashierShiftController::class, 'open'])->name('cashier-shifts.open');
    Route::get('cashier-shift/close', [CashierShiftController::class, 'closeForm'])->name('cashier-shifts.close-form');
    Route::post('cashier-shift/close', [CashierShiftController::class, 'close'])->name('cashier-shifts.close');

    Route::get('cash-transactions', [CashTransactionController::class, 'index'])->name('cash-transactions.index');
    Route::get('cash-transactions/create', [CashTransactionController::class, 'create'])->name('cash-transactions.create');
    Route::post('cash-transactions', [CashTransactionController::class, 'store'])->name('cash-transactions.store');

    Route::resource('expenses', ExpenseController::class)->only(['index', 'create', 'store', 'show']);

    Route::resource('price-levels', PriceLevelController::class)->except(['show']);

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('reports/purchases', [ReportController::class, 'purchases'])->name('reports.purchases');
    Route::get('reports/products', [ReportController::class, 'products'])->name('reports.products');
    Route::get('reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
    Route::get('reports/stock-opname', [ReportController::class, 'stockOpname'])->name('reports.stock-opname');
    Route::get('reports/receivable', [ReportController::class, 'receivable'])->name('reports.receivable');
    Route::get('reports/payable', [ReportController::class, 'payable'])->name('reports.payable');
    Route::get('reports/cash-flow', [ReportController::class, 'cashFlow'])->name('reports.cash-flow');
    Route::get('reports/profit-loss', [ReportController::class, 'profitLoss'])->name('reports.profit-loss');
    Route::get('reports/profitability', [ReportController::class, 'profitability'])->name('reports.profitability');
    Route::get('reports/customers', [ReportController::class, 'customers'])->name('reports.customers');
    Route::get('reports/suppliers', [ReportController::class, 'suppliers'])->name('reports.suppliers');
    Route::get('reports/projects', [ReportController::class, 'projects'])->name('reports.projects');
    Route::get('reports/deliveries', [ReportController::class, 'deliveries'])->name('reports.deliveries');
    Route::get('reports/sales/export', [ReportController::class, 'exportSales'])->name('reports.sales.export');
    Route::get('reports/purchases/export', [ReportController::class, 'exportPurchases'])->name('reports.purchases.export');

    Route::resource('users', UserController::class)->except(['show'])->middleware('permission:user.manage');
    Route::resource('roles', RoleController::class)->except(['show'])->middleware('permission:role.manage');

    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index')->middleware('permission:audit.view');

    Route::middleware('permission:settings.manage')->group(function () {
        Route::resource('taxes', TaxController::class)->except(['show']);
        Route::resource('payment-methods', PaymentMethodController::class)->except(['show']);
        Route::resource('units', UnitController::class)->except(['show']);
        Route::resource('warehouses', WarehouseController::class)->except(['show']);
        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });
});
