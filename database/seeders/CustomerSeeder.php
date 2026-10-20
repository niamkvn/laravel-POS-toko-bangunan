<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\PriceLevel;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['Budi Santoso', 'umum', 'UMUM', 0, 0],
            ['Agus Prasetyo', 'tukang', 'TUKANG', 5000000, 14],
            ['Slamet Riyadi', 'tukang', 'TUKANG', 5000000, 14],
            ['CV Bangun Jaya', 'kontraktor', 'KONTRAKTOR', 50000000, 30],
            ['PT Mitra Konstruksi', 'kontraktor', 'KONTRAKTOR', 100000000, 30],
            ['PT Graha Properti', 'developer', 'KONTRAKTOR', 200000000, 45],
            ['Toko Bangunan Sejahtera', 'reseller', 'GROSIR', 30000000, 21],
            ['UD Sumber Rejeki', 'reseller', 'GROSIR', 25000000, 21],
            ['PT Sinar Abadi', 'perusahaan', 'UMUM', 40000000, 30],
            ['Hendra Wijaya', 'umum', 'UMUM', 0, 0],
            ['Dewi Lestari', 'umum', 'UMUM', 0, 0],
            ['Kontraktor Amanah', 'kontraktor', 'KONTRAKTOR', 60000000, 30],
        ];

        foreach ($customers as $i => [$name, $type, $levelCode, $limit, $term]) {
            Customer::firstOrCreate(['customer_code' => 'CUST-'.str_pad($i + 1, 5, '0', STR_PAD_LEFT)], [
                'name' => $name,
                'customer_type' => $type,
                'price_level_id' => PriceLevel::where('code', $levelCode)->first()?->id,
                'credit_limit' => $limit,
                'payment_term_days' => $term,
                'phone' => '08'.rand(1000000000, 9999999999),
                'address' => 'Jl. Contoh Alamat No. '.($i + 1),
                'active' => true,
            ]);
        }
    }
}
