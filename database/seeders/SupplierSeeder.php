<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            'PT Semen Indonesia', 'PT Holcim Indonesia', 'PT Krakatau Steel',
            'CV Sumber Besi Jaya', 'PT Nippon Paint Indonesia', 'PT ICI Paints',
            'PT Mulia Keramik Indah', 'PT Arwana Citra Mulia', 'CV Kabel Sejahtera',
            'PT Supreme Cable', 'CV Pipa Mandiri', 'PT Wavin Indonesia',
        ];

        foreach ($suppliers as $i => $name) {
            Supplier::firstOrCreate(['supplier_code' => 'SUPP-'.str_pad($i + 1, 5, '0', STR_PAD_LEFT)], [
                'name' => $name,
                'company_name' => $name,
                'phone' => '021-'.rand(1000000, 9999999),
                'address' => 'Kawasan Industri Blok '.chr(65 + $i).' No. '.($i + 1),
                'payment_term_days' => [14, 30, 45][array_rand([14, 30, 45])],
                'credit_limit' => 100000000,
                'active' => true,
            ]);
        }
    }
}
