<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Opname {{ $stockOpname->opname_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #172033; padding: 30px; }
        .header { display: flex; justify-content: space-between; border-bottom: 3px solid #172033; padding-bottom: 15px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #172033; padding: 5px 7px; text-align: left; font-size: 11px; }
        th { background: #1E3A5F; color: #fff; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom:15px;"><button onclick="window.print()">Cetak</button></div>
    <div class="header">
        <div>
            <h2 style="margin:0;">{{ \App\Models\Setting::get('company_name', config('app.name')) }}</h2>
            <p style="margin:0;">Berita Acara Stock Opname</p>
        </div>
        <div style="text-align:right;">
            <h3 style="margin:0;">{{ $stockOpname->opname_number }}</h3>
            <p style="margin:0;">Gudang: {{ $stockOpname->warehouse->name ?? '-' }}</p>
            <p style="margin:0;">Tanggal: {{ $stockOpname->date->format('d F Y') }}</p>
        </div>
    </div>

    <table>
        <thead><tr><th>Produk</th><th>System</th><th>Physical</th><th>Selisih</th><th>Nilai</th><th>Alasan</th></tr></thead>
        <tbody>
            @foreach($stockOpname->items as $item)
            <tr>
                <td>{{ $item->product->name ?? '-' }}</td>
                <td>{{ $item->system_qty }}</td>
                <td>{{ $item->physical_qty ?? '-' }}</td>
                <td>{{ $item->difference_qty ?? '-' }}</td>
                <td>{{ $item->difference_value !== null ? number_format($item->difference_value,0,',','.') : '-' }}</td>
                <td>{{ $item->reason }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="display:flex;justify-content:space-between;margin-top:60px;">
        <div style="text-align:center;">Counter<br><br><br>({{ $stockOpname->counter->name ?? '-' }})</div>
        <div style="text-align:center;">Reviewer<br><br><br>({{ $stockOpname->reviewer->name ?? '-' }})</div>
        <div style="text-align:center;">Approver<br><br><br>({{ $stockOpname->approver->name ?? '-' }})</div>
    </div>
</body>
</html>
