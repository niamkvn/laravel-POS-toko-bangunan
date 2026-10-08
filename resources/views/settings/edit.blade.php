<x-app-layout title="Settings">
    <x-card title="Pengaturan Aplikasi">
        <form method="POST" action="{{ route('settings.update') }}">
            @csrf @method('PUT')
            <h6 class="mb-3">Informasi Perusahaan</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6"><x-input label="Nama Perusahaan" name="company_name" :value="$settings['company_name'] ?? config('app.name')" /></div>
                <div class="col-md-6"><x-input label="Telepon" name="company_phone" :value="$settings['company_phone'] ?? ''" /></div>
                <div class="col-md-12"><x-input label="Alamat" name="company_address" :value="$settings['company_address'] ?? ''" /></div>
                <div class="col-md-6"><x-input type="email" label="Email" name="company_email" :value="$settings['company_email'] ?? ''" /></div>
                <div class="col-md-6"><x-input label="Prefix Invoice" name="invoice_prefix" :value="$settings['invoice_prefix'] ?? 'INV'" /></div>
            </div>

            <h6 class="mb-3">Default &amp; Threshold</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4"><x-select label="Gudang Default" name="default_warehouse_id" :options="$warehouses" :selected="$settings['default_warehouse_id'] ?? null" /></div>
                <div class="col-md-4"><x-select label="Price Level Default" name="default_price_level_id" :options="$priceLevels" :selected="$settings['default_price_level_id'] ?? null" /></div>
                <div class="col-md-4"><x-input type="number" step="0.01" label="Low Stock Threshold (%)" name="low_stock_threshold" :value="$settings['low_stock_threshold'] ?? 0" /></div>
                <div class="col-md-6"><x-input type="number" step="0.01" label="Threshold Diskon Approval Manager (%)" name="discount_manager_threshold" :value="$settings['discount_manager_threshold'] ?? 5" /></div>
                <div class="col-md-6"><x-input type="number" step="0.01" label="Threshold Diskon Approval Owner (%)" name="discount_owner_threshold" :value="$settings['discount_owner_threshold'] ?? 15" /></div>
            </div>

            <h6 class="mb-3">Cetak</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6"><x-input label="Footer Struk" name="receipt_footer" :value="$settings['receipt_footer'] ?? 'Terima kasih atas kunjungan Anda'" /></div>
                <div class="col-md-6"><x-input label="Footer Invoice" name="invoice_footer" :value="$settings['invoice_footer'] ?? ''" /></div>
            </div>

            <button type="submit" class="btn btn-primary btn-nb">Simpan Pengaturan</button>
        </form>
    </x-card>
</x-app-layout>
