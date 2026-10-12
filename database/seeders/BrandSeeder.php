<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Semen Gresik', 'Holcim', 'Krakatau Steel', 'Nippon Paint', 'Dulux', 'Mulia Keramik', 'Roman', 'Supreme', 'Toto', 'Wavin'] as $name) {
            Brand::firstOrCreate(['name' => $name], ['active' => true]);
        }
    }
}
