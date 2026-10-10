<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = [
            ['WH-MAIN', 'Main Store', true],
            ['WH-STOCK', 'Main Warehouse', false],
            ['WH-IRON', 'Iron Warehouse', false],
            ['WH-CERAMIC', 'Ceramic Warehouse', false],
            ['WH-BACK', 'Back Warehouse', false],
        ];

        foreach ($warehouses as [$code, $name, $isDefault]) {
            Warehouse::firstOrCreate(['code' => $code], [
                'name' => $name, 'is_default' => $isDefault, 'active' => true,
                'address' => 'Jl. Raya Toko Bangunan No. 1',
            ]);
        }
    }
}
