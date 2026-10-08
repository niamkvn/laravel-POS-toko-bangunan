<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Warehouse;
use App\Models\PriceLevel;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    private array $keys = [
        'company_name', 'company_address', 'company_phone', 'company_email',
        'invoice_prefix', 'default_warehouse_id', 'default_price_level_id',
        'low_stock_threshold', 'discount_manager_threshold', 'discount_owner_threshold',
        'receipt_footer', 'invoice_footer',
    ];

    public function edit()
    {
        $settings = Setting::whereIn('key', $this->keys)->pluck('value', 'key');
        $warehouses = Warehouse::pluck('name', 'id');
        $priceLevels = PriceLevel::pluck('name', 'id');

        return view('settings.edit', compact('settings', 'warehouses', 'priceLevels'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'nullable|string|max:200',
            'company_address' => 'nullable|string',
            'company_phone' => 'nullable|string|max:30',
            'company_email' => 'nullable|email',
            'invoice_prefix' => 'nullable|string|max:10',
            'default_warehouse_id' => 'nullable|exists:warehouses,id',
            'default_price_level_id' => 'nullable|exists:price_levels,id',
            'low_stock_threshold' => 'nullable|numeric|min:0',
            'discount_manager_threshold' => 'nullable|numeric|min:0|max:100',
            'discount_owner_threshold' => 'nullable|numeric|min:0|max:100',
            'receipt_footer' => 'nullable|string',
            'invoice_footer' => 'nullable|string',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
