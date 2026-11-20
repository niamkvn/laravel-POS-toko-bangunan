<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $all = Permission::pluck('name')->all();

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions($all);

        $owner = Role::firstOrCreate(['name' => 'Owner', 'guard_name' => 'web']);
        $owner->syncPermissions($all);

        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $manager->syncPermissions(array_filter($all, fn ($p) => ! str_starts_with($p, 'user.') && ! str_starts_with($p, 'role.')));

        $kasir = Role::firstOrCreate(['name' => 'Kasir', 'guard_name' => 'web']);
        $kasir->syncPermissions([
            'dashboard.view', 'sales.view', 'sales.create', 'sales.discount',
            'customer.view', 'customer.create', 'payment.receive',
            'cashier_shift.view', 'cashier_shift.open', 'cashier_shift.close',
            'sales_return.view', 'sales_return.create', 'product.view', 'stock.view',
        ]);

        $gudang = Role::firstOrCreate(['name' => 'Gudang', 'guard_name' => 'web']);
        $gudang->syncPermissions([
            'dashboard.view', 'product.view', 'stock.view', 'stock.adjustment', 'stock.transfer',
            'stock.opname', 'goods_receipt.view', 'goods_receipt.create', 'delivery.view', 'delivery.edit',
        ]);

        $purchasing = Role::firstOrCreate(['name' => 'Purchasing', 'guard_name' => 'web']);
        $purchasing->syncPermissions([
            'dashboard.view', 'purchase.view', 'purchase.create', 'purchase.edit',
            'purchase_order.view', 'purchase_order.create', 'purchase_order.edit',
            'goods_receipt.view', 'goods_receipt.create', 'purchase_return.view', 'purchase_return.create',
            'supplier.view', 'supplier.create', 'supplier.edit', 'product.view', 'stock.view',
        ]);

        $accounting = Role::firstOrCreate(['name' => 'Accounting', 'guard_name' => 'web']);
        $accounting->syncPermissions([
            'dashboard.view', 'payment.receive', 'payment.supplier', 'cash.view', 'cash.create',
            'expense.view', 'expense.create', 'report.view', 'report.export', 'customer.view', 'supplier.view',
        ]);

        $sales = Role::firstOrCreate(['name' => 'Sales', 'guard_name' => 'web']);
        $sales->syncPermissions([
            'dashboard.view', 'quotation.view', 'quotation.create', 'quotation.edit',
            'sales_order.view', 'sales_order.create', 'sales_order.edit',
            'customer.view', 'customer.create', 'customer.edit', 'project.view', 'project.create',
            'sales.view', 'product.view',
        ]);

        $driver = Role::firstOrCreate(['name' => 'Driver', 'guard_name' => 'web']);
        $driver->syncPermissions(['dashboard.view', 'delivery.view', 'delivery.edit']);
    }
}
