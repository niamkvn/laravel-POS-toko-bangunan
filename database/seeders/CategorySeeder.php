<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $tree = [
            'Material' => ['Semen', 'Pasir', 'Batu', 'Besi', 'Bata', 'Baja Ringan'],
            'Finishing' => ['Cat', 'Keramik', 'Granit'],
            'Electrical' => ['Kabel', 'Lampu', 'Saklar', 'Stop Kontak'],
            'Plumbing' => ['Pipa', 'Fitting', 'Kran', 'Pompa'],
            'Tools' => ['Bor', 'Gerinda', 'Tang', 'Obeng'],
        ];

        foreach ($tree as $parentName => $children) {
            $parent = Category::firstOrCreate(
                ['slug' => Str::slug($parentName)],
                ['name' => $parentName, 'active' => true]
            );

            foreach ($children as $childName) {
                Category::firstOrCreate(
                    ['slug' => Str::slug($parentName.'-'.$childName)],
                    ['name' => $childName, 'parent_id' => $parent->id, 'active' => true]
                );
            }
        }
    }
}
