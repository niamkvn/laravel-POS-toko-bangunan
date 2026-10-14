<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $unit = fn (string $code) => Unit::where('code', $code)->first();
        $cat = fn (string $name) => Category::where('name', $name)->first();
        $inventory = app(InventoryService::class);
        $warehouses = Warehouse::all();
        $mainWarehouse = $warehouses->firstWhere('is_default', true) ?? $warehouses->first();

        $products = [
            // name, category, base_unit, purchase, sell, min_stock, extra_unit [code, factor, is_purchase, is_sale], stock
            ['Semen Gresik 50kg', 'Semen', 'SAK', 58000, 65000, 50, null, 500],
            ['Semen Holcim 50kg', 'Semen', 'SAK', 57000, 64000, 50, null, 400],
            ['Semen Putih 40kg', 'Semen', 'SAK', 62000, 70000, 20, null, 150],
            ['Pasir Beton per M3', 'Pasir', 'M3', 250000, 320000, 5, null, 50],
            ['Pasir Pasang per M3', 'Pasir', 'M3', 220000, 290000, 5, null, 40],
            ['Batu Split 2-3 per M3', 'Batu', 'M3', 260000, 330000, 5, null, 30],
            ['Batu Kali per M3', 'Batu', 'M3', 200000, 260000, 5, null, 25],
            ['Besi Beton 10mm', 'Besi', 'BATANG', 65000, 78000, 100, ['METER', 12, true, true], 800],
            ['Besi Beton 8mm', 'Besi', 'BATANG', 42000, 52000, 100, ['METER', 12, true, true], 700],
            ['Besi Beton 12mm', 'Besi', 'BATANG', 92000, 108000, 80, ['METER', 12, true, true], 600],
            ['Besi Hollow 4x4', 'Besi', 'BATANG', 75000, 90000, 50, ['METER', 6, true, true], 300],
            ['Bata Merah', 'Bata', 'PCS', 700, 1000, 2000, null, 20000],
            ['Bata Ringan (Hebel) 60x20x10', 'Bata', 'PCS', 8500, 11000, 500, null, 3000],
            ['Baja Ringan C75', 'Baja Ringan', 'BATANG', 55000, 68000, 100, ['METER', 6, true, true], 400],
            ['Baja Ringan Reng', 'Baja Ringan', 'BATANG', 28000, 36000, 100, ['METER', 6, true, true], 350],
            ['Cat Tembok Dulux 20kg', 'Cat', 'PAIL', 320000, 390000, 20, ['LITER', 20, false, true], 100],
            ['Cat Tembok Nippon 20kg', 'Cat', 'PAIL', 300000, 365000, 20, ['LITER', 20, false, true], 90],
            ['Cat Kayu Besi 5kg', 'Cat', 'PAIL', 145000, 180000, 15, ['LITER', 5, false, true], 60],
            ['Cat Primer 20kg', 'Cat', 'PAIL', 210000, 260000, 10, ['LITER', 20, false, true], 40],
            ['Keramik Lantai 40x40 Putih', 'Keramik', 'PCS', 5800, 7500, 200, ['DUS', 10, true, true], 3000],
            ['Keramik Lantai 60x60 Grey', 'Keramik', 'PCS', 15000, 19500, 150, ['DUS', 4, true, true], 1200],
            ['Keramik Dinding 25x40', 'Keramik', 'PCS', 4200, 5600, 200, ['DUS', 12, true, true], 2400],
            ['Granit 60x60 Polished', 'Granit', 'PCS', 42000, 55000, 100, ['DUS', 4, true, true], 800],
            ['Granit 80x80 Glossy', 'Granit', 'PCS', 68000, 88000, 60, ['DUS', 3, true, true], 400],
            ['Kabel NYM 2x1.5mm', 'Kabel', 'ROLL', 320000, 390000, 15, ['METER', 50, false, true], 100],
            ['Kabel NYA 1.5mm', 'Kabel', 'ROLL', 180000, 225000, 15, ['METER', 100, false, true], 80],
            ['Lampu LED 12W', 'Lampu', 'PCS', 18000, 25000, 100, ['BOX', 20, true, true], 600],
            ['Lampu Downlight 7W', 'Lampu', 'PCS', 22000, 30000, 80, ['BOX', 10, true, true], 400],
            ['Saklar Tunggal Broco', 'Saklar', 'PCS', 12000, 17000, 100, null, 500],
            ['Saklar Ganda Broco', 'Saklar', 'PCS', 18000, 25000, 80, null, 400],
            ['Stop Kontak Broco', 'Stop Kontak', 'PCS', 15000, 21000, 100, null, 450],
            ['Stop Kontak Outbow', 'Stop Kontak', 'PCS', 13000, 18000, 80, null, 350],
            ['Pipa PVC 3 inch Wavin', 'Pipa', 'BATANG', 65000, 82000, 60, ['METER', 4, true, true], 300],
            ['Pipa PVC 4 inch Wavin', 'Pipa', 'BATANG', 95000, 118000, 50, ['METER', 4, true, true], 250],
            ['Pipa PPR 1/2 inch', 'Pipa', 'BATANG', 35000, 45000, 60, ['METER', 4, true, true], 200],
            ['Fitting Knee PVC 3 inch', 'Fitting', 'PCS', 6500, 9000, 100, null, 500],
            ['Fitting Sock PVC 3 inch', 'Fitting', 'PCS', 4500, 6500, 100, null, 500],
            ['Kran Air Standar', 'Kran', 'PCS', 25000, 35000, 60, null, 250],
            ['Kran Wastafel Toto', 'Kran', 'PCS', 185000, 235000, 20, null, 80],
            ['Pompa Air Shimizu 125W', 'Pompa', 'UNIT', 650000, 780000, 10, null, 40],
            ['Pompa Air Sanyo 200W', 'Pompa', 'UNIT', 850000, 1020000, 8, null, 30],
            ['Bor Listrik Bosch', 'Bor', 'UNIT', 450000, 580000, 10, null, 25],
            ['Bor Baterai Makita', 'Bor', 'UNIT', 750000, 920000, 8, null, 20],
            ['Gerinda Tangan Bosch', 'Gerinda', 'UNIT', 380000, 480000, 10, null, 30],
            ['Gerinda Potong Makita', 'Gerinda', 'UNIT', 420000, 530000, 8, null, 20],
            ['Tang Kombinasi', 'Tang', 'PCS', 35000, 48000, 30, null, 100],
            ['Tang Potong', 'Tang', 'PCS', 28000, 39000, 30, null, 90],
            ['Obeng Set 6 in 1', 'Obeng', 'SET', 45000, 62000, 30, null, 80],
            ['Obeng Plus Besar', 'Obeng', 'PCS', 12000, 17000, 40, null, 150],
        ];

        foreach ($products as $index => $p) {
            [$name, $catName, $baseUnitCode, $purchasePrice, $sellPrice, $minStock, $extraUnit, $stock] = $p;
            $sku = 'SKU-'.str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            $code = 'PRD-'.str_pad($index + 1, 5, '0', STR_PAD_LEFT);
            $barcode = '899'.str_pad($index + 1, 9, '0', STR_PAD_LEFT);

            $product = Product::firstOrCreate(['sku' => $sku], [
                'barcode' => $barcode,
                'product_code' => $code,
                'name' => $name,
                'category_id' => $cat($catName)?->id,
                'unit_id' => $unit($baseUnitCode)->id,
                'purchase_price' => $purchasePrice,
                'default_sell_price' => $sellPrice,
                'average_cost' => $purchasePrice,
                'minimum_stock' => $minStock,
                'maximum_stock' => $minStock * 20,
                'active' => true,
            ]);

            if ($product->wasRecentlyCreated) {
                ProductUnit::create([
                    'product_id' => $product->id, 'unit_id' => $unit($baseUnitCode)->id,
                    'conversion_factor' => 1, 'is_base' => true, 'is_purchase_unit' => true, 'is_sale_unit' => true,
                ]);

                if ($extraUnit) {
                    [$extraCode, $factor, $isPurchase, $isSale] = $extraUnit;
                    ProductUnit::create([
                        'product_id' => $product->id, 'unit_id' => $unit($extraCode)->id,
                        'conversion_factor' => $factor, 'is_purchase_unit' => $isPurchase, 'is_sale_unit' => $isSale,
                    ]);
                }

                foreach ($warehouses as $warehouse) {
                    $qty = $warehouse->id === $mainWarehouse->id ? $stock : (int) ($stock * 0.15);
                    if ($qty > 0) {
                        $inventory->opening($product, $warehouse, $qty, $purchasePrice, 'Stok awal seeder');
                    }
                }
            }
        }
    }
}
