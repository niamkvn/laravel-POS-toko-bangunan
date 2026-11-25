# POS Toko Bangunan

Sistem POS + Inventory + Purchasing + Customer + Supplier + Finance + Warehouse + Project + Delivery Management untuk toko bangunan, dibangun dengan Laravel 13, MySQL, Blade, Bootstrap 5, dan Alpine.js.

Desain menggunakan konsep **Corporate Neo-Brutalism** dengan palet pastel blue: border tebal, hard shadow, tegas namun tetap profesional untuk kebutuhan software bisnis.

## Fitur Utama

- **POS Kasir** — cepat, keyboard-friendly, barcode scan, multi-satuan, split payment, hold/resume transaksi, penjualan tunai & kredit
- **Inventory** — multi gudang, multi satuan dengan konversi otomatis, stock card, stock transfer, stock opname (workflow counting → review → approve → post), stock adjustment dengan approval threshold
- **Purchasing** — Purchase Order → Goods Receipt (stok otomatis bertambah, HPP weighted-average) → Hutang → Pembayaran Supplier
- **Sales** — Quotation → Sales Order (partial fulfillment) → Invoice → Retur Penjualan
- **Piutang & Hutang** — dengan aging report, pembayaran partial/penuh, credit limit check
- **Delivery Management** — Delivery Order, Driver, Kendaraan
- **Project Management** — project customer dengan tracking transaksi & outstanding
- **Finance** — kas/bank multi akun, kasir shift, pengeluaran, mutasi kas
- **Laporan** — sales, purchase, stock, laba rugi, profitability, cash flow, customer/supplier ranking, dengan export CSV
- **Dashboard** — filter periode, business insight otomatis, top products/customers/suppliers, chart penjualan & metode pembayaran
- **RBAC** — role & permission granular (Super Admin, Owner, Manager, Kasir, Gudang, Purchasing, Accounting, Sales, Driver)
- **Audit Log** — mencatat seluruh aktivitas penting (login, create, update, void, approval, posting)

## Tech Stack

- Laravel 13, PHP 8.2+
- MySQL 8+
- Blade + Bootstrap 5 + Alpine.js
- Chart.js untuk dashboard
- Spatie Laravel Permission
- DomPDF & Laravel Excel untuk cetak/export

## Instalasi

1. Install dependency PHP dan JavaScript
   ```bash
   composer install
   npm install
   ```

2. Salin file environment dan generate application key
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Edit `.env`, isi `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` sesuai server MySQL Anda.

3. Buat database MySQL
   ```sql
   CREATE DATABASE pos_bangunan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

4. Jalankan migrasi & seeder (membuat seluruh tabel + data demo: role & permission, master data, 50 produk, customer, supplier, dan user demo)
   ```bash
   php artisan migrate --seed
   ```

5. Compile asset frontend
   ```bash
   npm run build
   ```

6. Jalankan server
   ```bash
   php artisan serve
   ```

7. Akses aplikasi di `http://localhost:8000`

### Demo Login

| Role       | Email                     | Password   |
|------------|---------------------------|------------|
| Super Admin| admin@example.com         | password   |
| Owner      | owner@example.com         | password   |
| Manager    | manager@example.com       | password   |
| Kasir      | cashier@example.com       | password   |
| Gudang     | warehouse@example.com     | password   |
| Purchasing | purchasing@example.com    | password   |
| Accounting | accounting@example.com    | password   |
| Sales      | sales@example.com         | password   |
| Driver     | driver@example.com        | password   |

## Testing

```bash
php artisan test
```

## Lisensi

Proprietary — seluruh hak cipta dilindungi.
# Sistem-POS-Toko-Bangunan
