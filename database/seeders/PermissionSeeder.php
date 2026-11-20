<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            'dashboard' => ['view'],
            'sales' => ['view', 'create', 'edit', 'delete', 'void', 'discount'],
            'quotation' => ['view', 'create', 'edit', 'delete'],
            'sales_order' => ['view', 'create', 'edit', 'delete'],
            'sales_return' => ['view', 'create'],
            'purchase' => ['view', 'create', 'edit', 'delete', 'approve'],
            'purchase_order' => ['view', 'create', 'edit', 'approve'],
            'goods_receipt' => ['view', 'create'],
            'purchase_return' => ['view', 'create'],
            'product' => ['view', 'create', 'edit', 'delete'],
            'category' => ['view', 'create', 'edit', 'delete'],
            'stock' => ['view', 'adjustment', 'transfer', 'opname', 'opname.approve'],
            'customer' => ['view', 'create', 'edit', 'delete'],
            'supplier' => ['view', 'create', 'edit', 'delete'],
            'project' => ['view', 'create', 'edit'],
            'delivery' => ['view', 'create', 'edit'],
            'payment' => ['receive', 'supplier'],
            'cashier_shift' => ['view', 'open', 'close'],
            'cash' => ['view', 'create'],
            'expense' => ['view', 'create'],
            'report' => ['view', 'export'],
            'audit' => ['view'],
            'user' => ['manage'],
            'role' => ['manage'],
            'settings' => ['manage'],
        ];

        foreach ($groups as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$module}.{$action}", 'guard_name' => 'web']);
            }
        }
    }
}
