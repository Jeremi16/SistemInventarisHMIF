<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok Inventaris HMIF</title>
    <style>
        body { color: #111827; font-family: Arial, sans-serif; margin: 32px; }
        h1 { font-size: 22px; margin: 0 0 4px; }
        p { color: #4b5563; margin: 0 0 18px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #d1d5db; font-size: 12px; padding: 8px; text-align: left; }
        th { background: #f3f4f6; }
        .summary { display: grid; gap: 10px; grid-template-columns: repeat(4, 1fr); margin: 20px 0; }
        .card { border: 1px solid #d1d5db; border-radius: 8px; padding: 12px; }
        .card span { color: #6b7280; display: block; font-size: 11px; }
        .card strong { display: block; font-size: 20px; margin-top: 4px; }
        .print { margin-bottom: 16px; }
        @media print { .print { display: none; } body { margin: 16px; } }
    </style>
</head>
<body>
    <button class="print" onclick="window.print()">Cetak / Simpan PDF</button>
    <h1>Laporan Stok Inventaris HMIF</h1>
    <p>Dicetak pada {{ now()->format('d M Y H:i') }}</p>

    <section class="summary">
        <div class="card"><span>Total Barang</span><strong>{{ $summary['total_items'] }}</strong></div>
        <div class="card"><span>Tersedia</span><strong>{{ $summary['total_available'] }}</strong></div>
        <div class="card"><span>Dipinjam</span><strong>{{ $summary['total_borrowed'] }}</strong></div>
        <div class="card"><span>Maintenance</span><strong>{{ $summary['total_maintenance'] }}</strong></div>
    </section>

    <table>
        <thead>
            <tr>
                <th>Barang</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>Status</th>
                <th>Kondisi</th>
                <th>Lokasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ ['available' => 'Tersedia', 'borrowed' => 'Dipinjam', 'maintenance' => 'Maintenance'][$item->status] ?? $item->status }}</td>
                    <td>{{ ['good' => 'Baik', 'fair' => 'Layak Pakai', 'damaged' => 'Rusak'][$item->condition] ?? $item->condition }}</td>
                    <td>{{ $item->location ?: '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
