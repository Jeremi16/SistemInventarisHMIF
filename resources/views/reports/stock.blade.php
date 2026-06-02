@extends('layouts.master')

@section('title', 'Laporan Stok')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Laporan Stok Terkini</h2>
            <p class="mt-1 text-sm text-gray-500">Data stok real-time berdasarkan inventaris aktif.</p>
        </div>
        <a href="{{ route('reports.stock.pdf') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-lg bg-hmif-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-hmif-700">
            Cetak PDF
        </a>
    </div>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        @foreach([
            ['label' => 'Total', 'value' => $summary['total_items']],
            ['label' => 'Tersedia', 'value' => $summary['total_available']],
            ['label' => 'Dipinjam', 'value' => $summary['total_borrowed']],
            ['label' => 'Maintenance', 'value' => $summary['total_maintenance']],
            ['label' => 'Peminjaman Aktif', 'value' => $summary['active_borrowings']],
        ] as $card)
            <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
                <p class="text-sm font-medium text-gray-500">{{ $card['label'] }}</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $card['value'] }}</p>
            </div>
        @endforeach
    </section>

    <form method="GET" action="{{ route('reports.stock') }}" class="grid gap-3 rounded-xl border border-gray-100 bg-white p-4 shadow-sm md:grid-cols-5">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barang" class="rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
        <select name="category" class="rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            <option value="">Semua Kategori</option>
            @foreach($categories as $category)
                <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            <option value="">Semua Status</option>
            @foreach(['available' => 'Tersedia', 'borrowed' => 'Dipinjam', 'maintenance' => 'Maintenance'] as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="condition" class="rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            <option value="">Semua Kondisi</option>
            @foreach(['good' => 'Baik', 'fair' => 'Layak Pakai', 'damaged' => 'Rusak'] as $value => $label)
                <option value="{{ $value }}" @selected(request('condition') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 rounded-lg bg-hmif-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-hmif-700">Filter</button>
            <a href="{{ route('reports.stock') }}" class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Reset</a>
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="border-b border-gray-100 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-5 py-4">Barang</th>
                        <th class="px-5 py-4">Kategori</th>
                        <th class="px-5 py-4">Stok</th>
                        <th class="px-5 py-4">Dipinjam</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Kondisi</th>
                        <th class="px-5 py-4">Lokasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4 font-semibold text-gray-900">{{ $item->name }}</td>
                            <td class="px-5 py-4">{{ $item->category }}</td>
                            <td class="px-5 py-4">{{ $item->quantity }}</td>
                            <td class="px-5 py-4">{{ $item->borrowings_count }}</td>
                            <td class="px-5 py-4">{{ ['available' => 'Tersedia', 'borrowed' => 'Dipinjam', 'maintenance' => 'Maintenance'][$item->status] ?? $item->status }}</td>
                            <td class="px-5 py-4">{{ ['good' => 'Baik', 'fair' => 'Layak Pakai', 'damaged' => 'Rusak'][$item->condition] ?? $item->condition }}</td>
                            <td class="px-5 py-4">{{ $item->location ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-gray-500">Tidak ada barang sesuai filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-100 px-5 py-4">
            {{ $items->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
