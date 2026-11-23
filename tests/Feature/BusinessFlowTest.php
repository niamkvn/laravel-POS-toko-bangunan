<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\InventoryService;
use App\Services\PurchaseService;
use App\Services\SalesService;
use App\Services\StockOpnameService;
use App\Services\StockTransferService;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BusinessFlowTest extends TestCase
{
    use RefreshDatabase;

    private Warehouse $warehouse;

    private Unit $unit;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);

        $this->admin = User::create([
            'employee_code' => 'EMP-TEST',
            'name' => 'Test Admin',
            'email' => 'admin@test.local',
            'password' => Hash::make('password'),
            'active' => true,
            'email_verified_at' => now(),
        ]);
        $this->admin->syncRoles(['Super Admin']);

        $this->warehouse = Warehouse::create(['code' => 'WH1', 'name' => 'Main Warehouse', 'is_default' => true, 'active' => true]);
        $this->unit = Unit::create(['code' => 'PCS', 'name' => 'Pieces']);
        PaymentMethod::create(['code' => 'CASH', 'name' => 'Cash', 'type' => 'cash', 'active' => true]);
    }

    private function makeProduct(string $name = 'Test Product', float $purchasePrice = 10000, float $sellPrice = 15000): Product
    {
        $product = Product::create([
            'sku' => 'SKU-'.uniqid(),
            'product_code' => 'PRD-'.uniqid(),
            'name' => $name,
            'unit_id' => $this->unit->id,
            'purchase_price' => $purchasePrice,
            'default_sell_price' => $sellPrice,
            'average_cost' => $purchasePrice,
            'minimum_stock' => 5,
            'active' => true,
        ]);

        ProductUnit::create([
            'product_id' => $product->id, 'unit_id' => $this->unit->id,
            'conversion_factor' => 1, 'is_base' => true, 'is_purchase_unit' => true, 'is_sale_unit' => true,
        ]);

        return $product;
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertOk();
    }

    public function test_user_can_authenticate_with_valid_credentials(): void
    {
        $response = $this->post('/login', ['email' => 'admin@test.local', 'password' => 'password']);
        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    public function test_creating_product(): void
    {
        $category = Category::create(['name' => 'Semen', 'slug' => 'semen', 'active' => true]);

        $this->actingAs($this->admin)->post('/products', [
            'name' => 'Semen Gresik 50kg',
            'sku' => 'SKU-SEMEN-1',
            'category_id' => $category->id,
            'unit_id' => $this->unit->id,
            'purchase_price' => 58000,
            'default_sell_price' => 65000,
            'minimum_stock' => 10,
            'active' => 1,
        ])->assertRedirect('/products');

        $this->assertDatabaseHas('products', ['sku' => 'SKU-SEMEN-1', 'name' => 'Semen Gresik 50kg']);
    }

    public function test_creating_customer(): void
    {
        $this->actingAs($this->admin)->post('/customers', [
            'name' => 'Budi Santoso',
            'customer_type' => 'umum',
            'credit_limit' => 0,
            'payment_term_days' => 0,
            'active' => 1,
        ])->assertRedirect('/customers');

        $this->assertDatabaseHas('customers', ['name' => 'Budi Santoso']);
    }

    public function test_sale_reduces_stock(): void
    {
        $product = $this->makeProduct();
        app(InventoryService::class)->opening($product, $this->warehouse, 100, 10000);

        $this->actingAs($this->admin);
        $sale = app(SalesService::class)->createSale([
            'warehouse_id' => $this->warehouse->id,
            'status' => 'completed',
            'items' => [['product_id' => $product->id, 'unit_id' => $this->unit->id, 'qty' => 5, 'price' => 15000]],
            'payments' => [['payment_method_id' => PaymentMethod::first()->id, 'amount' => 75000]],
        ]);

        $this->assertEquals(95, $product->stockInWarehouse($this->warehouse->id));
        $this->assertEquals('completed', $sale->status);
        $this->assertEquals(75000, $sale->total);
    }

    public function test_credit_sale_creates_receivable(): void
    {
        $product = $this->makeProduct();
        app(InventoryService::class)->opening($product, $this->warehouse, 100, 10000);
        $customer = Customer::create(['customer_code' => 'CUST-1', 'name' => 'Kontraktor A', 'customer_type' => 'kontraktor', 'credit_limit' => 10000000, 'payment_term_days' => 30, 'active' => true]);

        $this->actingAs($this->admin);
        $sale = app(SalesService::class)->createSale([
            'warehouse_id' => $this->warehouse->id,
            'customer_id' => $customer->id,
            'status' => 'completed',
            'items' => [['product_id' => $product->id, 'unit_id' => $this->unit->id, 'qty' => 10, 'price' => 15000]],
            'payments' => [['payment_method_id' => PaymentMethod::first()->id, 'amount' => 50000]],
        ]);

        $this->assertTrue($sale->is_credit);
        $this->assertEquals(100000, $sale->outstanding());
        $this->assertEquals(100000, $customer->totalReceivable());
    }

    public function test_payment_reduces_receivable(): void
    {
        $product = $this->makeProduct();
        app(InventoryService::class)->opening($product, $this->warehouse, 100, 10000);
        $customer = Customer::create(['customer_code' => 'CUST-2', 'name' => 'Kontraktor B', 'customer_type' => 'kontraktor', 'credit_limit' => 10000000, 'payment_term_days' => 30, 'active' => true]);

        $this->actingAs($this->admin);
        $sale = app(SalesService::class)->createSale([
            'warehouse_id' => $this->warehouse->id,
            'customer_id' => $customer->id,
            'status' => 'completed',
            'items' => [['product_id' => $product->id, 'unit_id' => $this->unit->id, 'qty' => 10, 'price' => 15000]],
            'payments' => [],
        ]);

        $this->assertEquals(150000, $sale->outstanding());

        $this->actingAs($this->admin)->post("/sales/{$sale->id}/receive-payment", [
            'amount' => 100000,
            'payment_method_id' => PaymentMethod::first()->id,
        ])->assertRedirect();

        $sale->refresh();
        $this->assertEquals(50000, $sale->outstanding());
    }

    public function test_purchase_increases_stock_and_creates_payable(): void
    {
        $product = $this->makeProduct();
        $supplier = Supplier::create(['supplier_code' => 'SUPP-1', 'name' => 'Supplier A', 'payment_term_days' => 30, 'credit_limit' => 0, 'active' => true]);

        $this->actingAs($this->admin);
        $receipt = app(PurchaseService::class)->receiveGoods([
            'supplier_id' => $supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'items' => [['product_id' => $product->id, 'unit_id' => $this->unit->id, 'qty' => 20, 'unit_cost' => 10000]],
        ]);

        $this->assertEquals(20, $product->stockInWarehouse($this->warehouse->id));

        $purchase = $receipt->purchase;
        $this->assertNotNull($purchase);
        $this->assertEquals(200000, $purchase->total);
        $this->assertEquals('unpaid', $purchase->status);
        $this->assertEquals(200000, $supplier->totalPayable());
    }

    public function test_stock_transfer_moves_stock_between_warehouses(): void
    {
        $product = $this->makeProduct();
        $destination = Warehouse::create(['code' => 'WH2', 'name' => 'Second Warehouse', 'active' => true]);
        app(InventoryService::class)->opening($product, $this->warehouse, 50, 10000);

        $this->actingAs($this->admin);
        $service = app(StockTransferService::class);
        $transfer = $service->create([
            'source_warehouse_id' => $this->warehouse->id,
            'destination_warehouse_id' => $destination->id,
            'transfer_date' => now(),
            'items' => [['product_id' => $product->id, 'unit_id' => $this->unit->id, 'qty' => 15]],
        ]);
        $service->approve($transfer);
        $service->ship($transfer);

        $this->assertEquals(35, $product->stockInWarehouse($this->warehouse->id));

        $service->receive($transfer);
        $this->assertEquals(15, $product->stockInWarehouse($destination->id));
    }

    public function test_stock_opname_workflow_approval_and_posting(): void
    {
        $product = $this->makeProduct();
        app(InventoryService::class)->opening($product, $this->warehouse, 150, 10000);

        $this->actingAs($this->admin);
        $service = app(StockOpnameService::class);

        $opname = $service->create($this->warehouse->id, now()->format('Y-m-d'));
        $this->assertEquals('counting', $opname->status);

        $item = $opname->items->first();
        $service->saveCount($opname, [['id' => $item->id, 'physical_qty' => 147, 'reason' => 'missing']]);

        $item->refresh();
        $this->assertEquals(-3, (float) $item->difference_qty);

        // Stock must NOT change before posting
        $this->assertEquals(150, $product->stockInWarehouse($this->warehouse->id));

        $service->submitReview($opname);
        $this->assertEquals('review', $opname->fresh()->status);

        $service->approve($opname);
        $this->assertEquals('approved', $opname->fresh()->status);

        $service->post($opname);
        $opname->refresh();
        $this->assertEquals('posted', $opname->status);
        $this->assertEquals(147, $product->stockInWarehouse($this->warehouse->id));
    }

    public function test_sales_return_restores_stock_and_cannot_exceed_sold_qty(): void
    {
        $product = $this->makeProduct();
        app(InventoryService::class)->opening($product, $this->warehouse, 100, 10000);

        $this->actingAs($this->admin);
        $sale = app(SalesService::class)->createSale([
            'warehouse_id' => $this->warehouse->id,
            'status' => 'completed',
            'items' => [['product_id' => $product->id, 'unit_id' => $this->unit->id, 'qty' => 10, 'price' => 15000]],
            'payments' => [['payment_method_id' => PaymentMethod::first()->id, 'amount' => 150000]],
        ]);

        $this->assertEquals(90, $product->stockInWarehouse($this->warehouse->id));

        $saleItem = $sale->items->first();

        $this->post('/sale-returns', [
            'sale_id' => $sale->id,
            'items' => [['sale_item_id' => $saleItem->id, 'qty' => 4]],
        ])->assertRedirect();

        $this->assertEquals(94, $product->stockInWarehouse($this->warehouse->id));
        $this->assertEquals(4, $saleItem->fresh()->returned_qty);

        // Attempting to return more than remaining returnable qty must fail validation
        $response = $this->post('/sale-returns', [
            'sale_id' => $sale->id,
            'items' => [['sale_item_id' => $saleItem->id, 'qty' => 10]],
        ]);
        $response->assertSessionHasErrors();
        $this->assertEquals(94, $product->stockInWarehouse($this->warehouse->id));
    }

    public function test_purchase_return_reduces_stock_and_payable(): void
    {
        $product = $this->makeProduct();
        $supplier = Supplier::create(['supplier_code' => 'SUPP-2', 'name' => 'Supplier B', 'payment_term_days' => 30, 'credit_limit' => 0, 'active' => true]);

        $this->actingAs($this->admin);
        $receipt = app(PurchaseService::class)->receiveGoods([
            'supplier_id' => $supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'items' => [['product_id' => $product->id, 'unit_id' => $this->unit->id, 'qty' => 20, 'unit_cost' => 10000]],
        ]);
        $purchase = $receipt->purchase;
        $purchaseItem = $purchase->items->first();

        $this->post('/purchase-returns', [
            'purchase_id' => $purchase->id,
            'items' => [['purchase_item_id' => $purchaseItem->id, 'qty' => 5]],
        ])->assertRedirect();

        $this->assertEquals(15, $product->stockInWarehouse($this->warehouse->id));
        $this->assertEquals(150000, $purchase->fresh()->total);
    }

    public function test_kasir_cannot_access_user_management(): void
    {
        $kasir = User::create([
            'employee_code' => 'EMP-KASIR',
            'name' => 'Kasir Test',
            'email' => 'kasir@test.local',
            'password' => Hash::make('password'),
            'active' => true,
            'email_verified_at' => now(),
        ]);
        $kasir->syncRoles(['Kasir']);

        $this->actingAs($kasir)->get('/users')->assertForbidden();
        $this->actingAs($kasir)->get('/dashboard')->assertOk();
    }
}
