@extends('layouts.master')

@section('title', 'Laporan Peminjaman')

@section('content')
@php
    $statusLabels = [
        'pending' => 'Menunggu',
        'approved' => 'Siap Diambil',
        'rejected' => 'Ditolak',
        'borrowed' => 'Dipinjam',
        'returned' => 'Dikembalikan',
        'overdue' => 'Terlambat',
    ];
@endphp

<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <h2 class="text-xl font-bold text-gray-900 sm:text-2xl">Laporan Peminjaman</h2>
            <p class="mt-1 text-sm text-gray-500">Rekap transaksi peminjaman dan frekuensi penggunaan barang.</p>
        </div>
        <a href="{{ route('reports.borrowing.pdf', request()->query()) }}" target="_blank" rel="noopener noreferrer" class="inline-flex w-full items-center justify-center rounded-lg bg-hmif-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-hmif-700 sm:w-auto">
            Cetak PDF
        </a>
    </div>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
        @foreach([
            'Total' => $stats['total'],
            'Menunggu' => $stats['pending'],
            'Siap Diambil' => $stats['approved'],
            'Aktif' => $stats['borrowed'],
            'Kembali' => $stats['returned'],
            'Ditolak' => $stats['rejected'],
        ] as $label => $value)
            <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
                <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $value }}</p>
            </div>
        @endforeach
    </section>

    <form method="GET" action="{{ route('reports.borrowing') }}" data-auto-filter class="grid gap-3 rounded-xl border border-gray-100 bg-white p-4 shadow-sm md:grid-cols-5">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Barang atau peminjam" class="rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
        <select name="status" class="rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            <option value="">Semua Status</option>
            @foreach($statusLabels as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
        <div class="flex flex-col gap-2 sm:flex-row">
            <button type="submit" class="flex-1 rounded-lg bg-hmif-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-hmif-700">Filter</button>
            <a href="{{ route('reports.borrowing') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Reset</a>
        </div>
    </form>

    <section class="grid gap-4 xl:grid-cols-2">
        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <h3 class="font-semibold text-gray-900">Barang Paling Sering Dipinjam</h3>
            <div class="mt-4 space-y-3">
                @forelse($topItems as $item)
                    <div class="flex items-center justify-between gap-3 rounded-lg bg-gray-50 px-4 py-3 text-sm">
                        <span class="hmif-break-anywhere font-medium text-gray-800">{{ $item->item_name }}</span>
                        <span class="font-bold text-hmif-700">{{ $item->total }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Belum ada data.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <h3 class="font-semibold text-gray-900">Peminjam Teraktif</h3>
            <div class="mt-4 space-y-3">
                @forelse($topBorrowers as $borrower)
                    <div class="flex items-center justify-between gap-3 rounded-lg bg-gray-50 px-4 py-3 text-sm">
                        <span class="hmif-break-anywhere">
                            <span class="font-medium text-gray-800">{{ $borrower->borrower_name }}</span>
                            <span class="text-gray-400">({{ $borrower->borrower_nim }})</span>
                        </span>
                        <span class="font-bold text-hmif-700">{{ $borrower->total }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Belum ada data.</p>
                @endforelse
            </div>
        </div>
    </section>

    <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="hmif-table-scroll">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="border-b border-gray-100 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-5 py-4">Peminjam</th>
                        <th class="px-5 py-4">Barang</th>
                        <th class="px-5 py-4">Durasi</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Foto Pengembalian</th>
                        <th class="px-5 py-4">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($borrowings as $borrowing)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4">
                                <p class="hmif-break-anywhere font-semibold text-gray-900">{{ $borrowing->borrower_name }}</p>
                                <p class="text-xs text-gray-400">{{ $borrowing->borrower_nim ?: '-' }}</p>
                            </td>
                            <td class="hmif-break-anywhere px-5 py-4">{{ $borrowing->item_name }}</td>
                            <td class="whitespace-nowrap px-5 py-4">{{ $borrowing->startDateTime()?->format('d M Y H:i:s') }} - {{ $borrowing->endDateTime()?->format('d M Y H:i:s') }}</td>
                            <td class="px-5 py-4">{{ $statusLabels[$borrowing->status] ?? $borrowing->status }}</td>
                            <td class="px-5 py-4">
                                @if($borrowing->return_photo)
                                    <a href="{{ asset('storage/' . $borrowing->return_photo) }}" target="_blank" rel="noopener noreferrer" class="block h-14 w-20 overflow-hidden rounded-lg border border-gray-200">
                                        <img src="{{ asset('storage/' . $borrowing->return_photo) }}" alt="Foto pengembalian {{ $borrowing->item_name }}" class="h-full w-full object-cover">
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="hmif-break-anywhere px-5 py-4">{{ $borrowing->admin_note ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-500">Tidak ada peminjaman sesuai filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-100 px-5 py-4">
            {{ $borrowings->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
