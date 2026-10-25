<?php

namespace Database\Seeders;

use App\Models\CashAccount;
use Illuminate\Database\Seeder;

class CashAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['CASH-TOKO', 'Kas Toko', 'cash', 10000000],
            ['BCA', 'Bank BCA', 'bank', 25000000],
            ['MANDIRI', 'Bank Mandiri', 'bank', 15000000],
            ['QRIS', 'QRIS', 'ewallet', 0],
        ];

        foreach ($accounts as [$code, $name, $type, $opening]) {
            CashAccount::firstOrCreate(['code' => $code], [
                'name' => $name, 'type' => $type, 'opening_balance' => $opening,
                'current_balance' => $opening, 'active' => true,
            ]);
        }
    }
}
