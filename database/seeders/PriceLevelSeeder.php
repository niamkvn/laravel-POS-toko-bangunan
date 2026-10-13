<?php

namespace Database\Seeders;

use App\Models\PriceLevel;
use Illuminate\Database\Seeder;

class PriceLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['UMUM', 'Umum', true],
            ['TUKANG', 'Tukang', false],
            ['KONTRAKTOR', 'Kontraktor', false],
            ['GROSIR', 'Grosir', false],
            ['RESELLER', 'Reseller', false],
        ];

        foreach ($levels as [$code, $name, $default]) {
            PriceLevel::firstOrCreate(['code' => $code], ['name' => $name, 'is_default' => $default]);
        }
    }
}
