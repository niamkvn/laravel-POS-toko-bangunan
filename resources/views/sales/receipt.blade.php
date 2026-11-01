<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk {{ $sale->invoice_number }}</title>
    <style>
        body { font-family: 'Courier New', monospace; font-size: 12px; width: 80mm; margin: 0 auto; padding: 10px; color:#000; }
        .center { text-align: center; }
        .line { border-top: 1px dashed #000; margin: 6px 0; }
        table { width: 100%; font-size: 11px; }
        td { padding: 1px 0; }
        .right { text-align: right; }
        @media print { .no-print { display: none; } body { width: 80mm; } }
    </style>
</head>
<body>
    <div class="no-print center" style="margin-bottom:10px;">
        <button onclick="window.print()">Cetak Struk</button>
        <a href="{{ route('sales.index') }}">Kembali</a>
    </div>

    <div class="center">
        <strong>{{ \App\Models\Setting::get('company_name', config('app.name')) }}</strong><br>
        {{ \App\Models\Setting::get('company_address', '') }}<br>
        {{ \App\Models\Setting::get('company_phone', '') }}
    </div>
    <div class="line"></div>
    <div>{{ $sale->invoice_number }}<br>{{ $sale->sale_date->format('d/m/Y H:i') }}<br>Kasir: {{ auth()->user()->name }}<br>Customer: {{ $sale->customer->name ?? 'Umum' }}</div>
    <div class="line"></div>

    <table>
        @foreach($sale->items as $item)
        <tr><td colspan="2">{{ $item->product->name ?? '-' }}</td></tr>
        <tr>
            <td>{{ $item->qty }} {{ $item->unit->code ?? '' }} x {{ number_format($item->price,0,',','.') }}</td>
            <td class="right">{{ number_format($item->subtotal,0,',','.') }}</td>
        </tr>
        @endforeach
    </table>

    <div class="line"></div>
    <table>
        <tr><td>Subtotal</td><td class="right">{{ number_format($sale->subtotal,0,',','.') }}</td></tr>
        <tr><td>Diskon</td><td class="right">{{ number_format($sale->discount_amount,0,',','.') }}</td></tr>
        <tr><td><strong>TOTAL</strong></td><td class="right"><strong>{{ number_format($sale->total,0,',','.') }}</strong></td></tr>
        @foreach($sale->payments as $p)
        <tr><td>{{ $p->paymentMethod->name ?? '-' }}</td><td class="right">{{ number_format($p->amount,0,',','.') }}</td></tr>
        @endforeach
        <tr><td>Kembali</td><td class="right">{{ number_format($sale->change_amount,0,',','.') }}</td></tr>
    </table>
    <div class="line"></div>
    <div class="center">{{ \App\Models\Setting::get('receipt_footer', 'Terima kasih atas kunjungan Anda') }}</div>
</body>
</html>
