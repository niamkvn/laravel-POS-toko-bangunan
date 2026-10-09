<?php

namespace Database\Seeders;

use App\Models\Tax;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    public function run(): void
    {
        Tax::firstOrCreate(['name' => 'PPN 11%'], ['rate' => 11, 'is_default' => true, 'active' => true]);
        Tax::firstOrCreate(['name' => 'Non PPN'], ['rate' => 0, 'is_default' => false, 'active' => true]);
    }
}
