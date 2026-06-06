<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Peminjaman HMIF</title>
    <style>
        body { color: #111827; font-family: Arial, sans-serif; margin: 32px; }
        h1 { font-size: 22px; margin: 0 0 4px; }
        p { color: #4b5563; margin: 0 0 18px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #d1d5db; font-size: 12px; padding: 8px; text-align: left; vertical-align: top; }
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
    @php
        $statusLabels = [
            'pending' => 'Menunggu',
            'approved' => 'Siap Diambil',
            'rejected' => 'Ditolak',
            'borrowed' => 'Diterima',
            'returned' => 'Dikembalikan',
            'overdue' => 'Terlambat',
        ];
    @endphp

    <button class="print" onclick="window.print()">Cetak / Simpan PDF</button>
    <h1>Laporan Peminjaman HMIF</h1>
    <p>Dicetak pada {{ now()->format('d M Y H:i') }}</p>

    <section class="summary">
        <div class="card"><span>Total</span><strong>{{ $stats['total'] }}</strong></div>
        <div class="card"><span>Dikembalikan</span><strong>{{ $stats['returned'] }}</strong></div>
        <div class="card"><span>Menunggu</span><strong>{{ $stats['pending'] }}</strong></div>
        <div class="card"><span>Aktif</span><strong>{{ $stats['active'] }}</strong></div>
    </section>

    <table>
        <thead>
            <tr>
                <th>Peminjam</th>
                <th>Barang</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
                <th>Status</th>
                <th>Foto Pengembalian</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($borrowings as $borrowing)
                <tr>
                    <td>{{ $borrowing->borrower_name }}<br>{{ $borrowing->borrower_nim ?: '-' }}</td>
                    <td>{{ $borrowing->item_name }}</td>
                    <td>{{ $borrowing->startDateTime()?->format('d M Y H:i:s') }}</td>
                    <td>{{ $borrowing->endDateTime()?->format('d M Y H:i:s') }}</td>
                    <td>{{ $statusLabels[$borrowing->status] ?? $borrowing->status }}</td>
                    <td>{{ $borrowing->return_photo ? 'Ada' : '-' }}</td>
                    <td>{{ $borrowing->admin_note ?: '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
