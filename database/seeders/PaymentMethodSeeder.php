<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['CASH', 'Cash', 'cash'],
            ['TRANSFER', 'Bank Transfer', 'bank_transfer'],
            ['QRIS', 'QRIS', 'qris'],
            ['EWALLET', 'E-Wallet', 'ewallet'],
            ['CREDIT', 'Credit', 'credit'],
        ];

        foreach ($methods as [$code, $name, $type]) {
            PaymentMethod::firstOrCreate(['code' => $code], ['name' => $name, 'type' => $type, 'active' => true]);
        }
    }
}
