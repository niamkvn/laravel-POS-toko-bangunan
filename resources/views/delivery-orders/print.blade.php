<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>DO {{ $deliveryOrder->do_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; color: #172033; padding: 30px; }
        .header { display: flex; justify-content: space-between; border-bottom: 3px solid #172033; padding-bottom: 15px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #172033; padding: 6px 8px; text-align: left; font-size: 12px; }
        th { background: #1E3A5F; color: #fff; }
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
            <h3 style="margin:0;">SURAT JALAN</h3>
            <p style="margin:0;">{{ $deliveryOrder->do_number }}</p>
            <p style="margin:0;">{{ $deliveryOrder->delivery_date->format('d F Y') }}</p>
        </div>
    </div>

    <p><strong>Kepada:</strong> {{ $deliveryOrder->customer->name ?? '-' }}<br>{{ $deliveryOrder->delivery_address }}<br>
    <strong>Driver:</strong> {{ $deliveryOrder->driver->name ?? '-' }} — <strong>Kendaraan:</strong> {{ $deliveryOrder->vehicle->plate_number ?? '-' }}</p>

    <table>
        <thead><tr><th>Produk</th><th>Qty</th></tr></thead>
        <tbody>
            @foreach($deliveryOrder->items as $item)
            <tr><td>{{ $item->product->name ?? '-' }}</td><td>{{ $item->qty }} {{ $item->unit->code ?? '' }}</td></tr>
            @endforeach
        </tbody>
    </table>

    <div style="display:flex;justify-content:space-between;margin-top:60px;">
        <div style="text-align:center;">Pengirim<br><br><br>(....................)</div>
        <div style="text-align:center;">Penerima<br><br><br>(....................)</div>
    </div>
</body>
</html>
