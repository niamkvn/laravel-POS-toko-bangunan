<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $sale->invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; color: #172033; padding: 30px; }
        .header { display: flex; justify-content: space-between; border-bottom: 3px solid #172033; padding-bottom: 15px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #172033; padding: 6px 8px; text-align: left; font-size: 12px; }
        th { background: #1E3A5F; color: #fff; }
        .totals { width: 300px; margin-left: auto; margin-top: 15px; }
        .totals div { display: flex; justify-content: space-between; padding: 3px 0; }
        .totals .grand { font-weight: bold; font-size: 16px; border-top: 2px solid #172033; margin-top: 5px; padding-top: 5px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom:15px;"><button onclick="window.print()">Cetak</button></div>
    <div class="header">
        <div>
            <h2 style="margin:0;">{{ \App\Models\Setting::get('company_name', config('app.name')) }}</h2>
            <p style="margin:0;">{{ \App\Models\Setting::get('company_address', '') }}</p>
            <p style="margin:0;">{{ \App\Models\Setting::get('company_phone', '') }}</p>
        </div>
        <div style="text-align:right;">
            <h3 style="margin:0;">INVOICE</h3>
            <p style="margin:0;">{{ $sale->invoice_number }}</p>
            <p style="margin:0;">{{ $sale->sale_date->format('d F Y H:i') }}</p>
        </div>
    </div>

    <p><strong>Customer:</strong> {{ $sale->customer->name ?? 'Umum' }}<br>
    <strong>Gudang:</strong> {{ $sale->warehouse->name ?? '-' }}</p>

    <table>
        <thead><tr><th>Produk</th><th>Qty</th><th>Harga</th><th>Diskon</th><th>Subtotal</th></tr></thead>
        <tbody>
            @foreach($sale->items as $item)
            <tr>
                <td>{{ $item->product->name ?? '-' }}</td>
                <td>{{ $item->qty }} {{ $item->unit->code ?? '' }}</td>
                <td>Rp {{ number_format($item->price,0,',','.') }}</td>
                <td>Rp {{ number_format($item->discount_amount,0,',','.') }}</td>
                <td>Rp {{ number_format($item->subtotal,0,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div><span>Subtotal</span><span>Rp {{ number_format($sale->subtotal,0,',','.') }}</span></div>
        <div><span>Diskon</span><span>Rp {{ number_format($sale->discount_amount,0,',','.') }}</span></div>
        <div><span>Pajak</span><span>Rp {{ number_format($sale->tax_amount,0,',','.') }}</span></div>
        <div class="grand"><span>Total</span><span>Rp {{ number_format($sale->total,0,',','.') }}</span></div>
        <div><span>Dibayar</span><span>Rp {{ number_format($sale->paid_amount,0,',','.') }}</span></div>
        @if($sale->is_credit)
        <div><span>Sisa Piutang</span><span>Rp {{ number_format($sale->outstanding(),0,',','.') }}</span></div>
        @endif
    </div>

    <p style="margin-top:40px;">{{ \App\Models\Setting::get('invoice_footer', 'Terima kasih atas kepercayaan Anda.') }}</p>
</body>
</html>
