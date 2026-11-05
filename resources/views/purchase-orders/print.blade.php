<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PO {{ $purchaseOrder->po_number }}</title>
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
        </div>
        <div style="text-align:right;">
            <h3 style="margin:0;">PURCHASE ORDER</h3>
            <p style="margin:0;">{{ $purchaseOrder->po_number }}</p>
            <p style="margin:0;">{{ $purchaseOrder->order_date->format('d F Y') }}</p>
        </div>
    </div>

    <p><strong>Kepada Supplier:</strong> {{ $purchaseOrder->supplier->name ?? '-' }}<br>{{ $purchaseOrder->supplier->address ?? '' }}<br>
    <strong>Kirim ke:</strong> {{ $purchaseOrder->warehouse->name ?? '-' }}</p>

    <table>
        <thead><tr><th>Produk</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr></thead>
        <tbody>
            @foreach($purchaseOrder->items as $item)
            <tr>
                <td>{{ $item->product->name ?? '-' }}</td>
                <td>{{ $item->qty }} {{ $item->unit->code ?? '' }}</td>
                <td>Rp {{ number_format($item->price,0,',','.') }}</td>
                <td>Rp {{ number_format($item->subtotal,0,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="grand"><span>Total</span><span>Rp {{ number_format($purchaseOrder->total,0,',','.') }}</span></div>
    </div>

    <div style="display:flex;justify-content:space-between;margin-top:60px;">
        <div style="text-align:center;">Dibuat oleh<br><br><br>({{ $purchaseOrder->creator->name ?? '-' }})</div>
        <div style="text-align:center;">Disetujui oleh<br><br><br>({{ $purchaseOrder->approver->name ?? '-' }})</div>
    </div>
</body>
</html>
