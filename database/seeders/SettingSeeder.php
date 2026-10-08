<?php

namespace Database\Seeders;

use App\Models\ApprovalThreshold;
use App\Models\PriceLevel;
use App\Models\Setting;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaultWarehouse = Warehouse::where('is_default', true)->first();
        $defaultLevel = PriceLevel::where('is_default', true)->first();

        $settings = [
            'company_name' => 'Toko Bangunan Jaya Makmur',
            'company_address' => 'Jl. Industri Raya No. 45, Jakarta',
            'company_phone' => '021-5551234',
            'company_email' => 'info@tokobangunan.test',
            'invoice_prefix' => 'INV',
            'default_warehouse_id' => $defaultWarehouse?->id,
            'default_price_level_id' => $defaultLevel?->id,
            'low_stock_threshold' => 10,
            'discount_manager_threshold' => 5,
            'discount_owner_threshold' => 15,
            'receipt_footer' => 'Terima kasih atas kunjungan Anda',
            'invoice_footer' => 'Barang yang sudah dibeli tidak dapat ditukar kecuali cacat produksi.',
        ];

        foreach ($settings as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }

        $thresholds = [
            ['discount_manager', 'Diskon butuh approval Manager', 5, 'Manager'],
            ['discount_owner', 'Diskon butuh approval Owner', 15, 'Owner'],
            ['stock_adjustment', 'Stock adjustment besar butuh approval Manager', 50, 'Manager'],
            ['void_transaction', 'Void transaksi butuh approval Manager/Owner', null, 'Manager'],
        ];

        foreach ($thresholds as [$code, $label, $value, $role]) {
            ApprovalThreshold::firstOrCreate(['code' => $code], [
                'label' => $label, 'threshold_value' => $value, 'role_required' => $role, 'active' => true,
            ]);
        }
    }
}
