@extends('layouts.master')

@section('title', 'Laporan')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Laporan Inventaris</h2>
    </div>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['label' => 'Total Barang', 'value' => $summary['total_items'], 'class' => 'bg-hmif-100 text-hmif-700'],
            ['label' => 'Tersedia', 'value' => $summary['available_items'], 'class' => 'bg-green-100 text-green-700'],
            ['label' => 'Peminjaman Aktif', 'value' => $summary['active_borrowings'], 'class' => 'bg-blue-100 text-blue-700'],
            ['label' => 'Terlambat', 'value' => $summary['overdue_borrowings'], 'class' => 'bg-red-100 text-red-700'],
        ] as $card)
            <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ $card['label'] }}</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $card['value'] }}</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-lg {{ $card['class'] }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.5L19 9.5V19a2 2 0 01-2 2Z"/>
                        </svg>
                    </div>
                </div>
            </div>
        @endforeach
    </section>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['title' => 'Laporan Stok', 'desc' => 'Stok tersedia, dipinjam, dan maintenance.', 'route' => route('reports.stock')],
            ['title' => 'Laporan Peminjaman', 'desc' => 'Rekap transaksi, status, dan peminjam aktif.', 'route' => route('reports.borrowing')],
            ['title' => 'Laporan Kondisi', 'desc' => 'Ringkasan kondisi barang inventaris.', 'route' => route('reports.condition')],
            ['title' => 'Histori Transaksi', 'desc' => 'Riwayat barang masuk dan keluar.', 'route' => route('reports.history')],
        ] as $report)
            <a href="{{ $report['route'] }}" class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm transition hover:border-hmif-200 hover:shadow-md">
                <h3 class="font-semibold text-gray-900">{{ $report['title'] }}</h3>
                <p class="mt-2 text-sm leading-6 text-gray-500">{{ $report['desc'] }}</p>
                <span class="mt-4 inline-flex text-sm font-semibold text-hmif-700">Buka laporan</span>
            </a>
        @endforeach
    </section>
</div>
@endsection
