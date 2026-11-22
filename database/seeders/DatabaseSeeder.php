<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            UnitSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            WarehouseSeeder::class,
            PriceLevelSeeder::class,
            TaxSeeder::class,
            PaymentMethodSeeder::class,
            CashAccountSeeder::class,
            ExpenseCategorySeeder::class,
            SettingSeeder::class,
            ProductSeeder::class,
            CustomerSeeder::class,
            SupplierSeeder::class,
            DriverVehicleSeeder::class,
        ]);
    }
}
