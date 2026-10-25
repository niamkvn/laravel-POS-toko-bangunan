<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Listrik', 'BBM', 'Gaji', 'Transport', 'Sewa', 'ATK', 'Maintenance', 'Kendaraan', 'Lainnya'] as $name) {
            ExpenseCategory::firstOrCreate(['name' => $name], ['active' => true]);
        }
    }
}
