@extends('layouts.master')

@section('title', 'Laporan Kondisi')

@section('content')
<div class="space-y-6">
    <div class="min-w-0">
        <h2 class="text-xl font-bold text-gray-900 sm:text-2xl">Laporan Kondisi Barang</h2>
        <p class="mt-1 text-sm text-gray-500">Ringkasan kondisi dan status inventaris aktif.</p>
    </div>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
        @foreach([
            'Baik' => $conditionSummary['good'],
            'Layak Pakai' => $conditionSummary['fair'],
            'Rusak' => $conditionSummary['damaged'],
            'Tersedia' => $statusSummary['available'],
            'Dipinjam' => $statusSummary['borrowed'],
            'Maintenance' => $statusSummary['maintenance'],
        ] as $label => $value)
            <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
                <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $value }}</p>
            </div>
        @endforeach
    </section>

    <form method="GET" action="{{ route('reports.condition') }}" data-auto-filter class="flex flex-col gap-3 rounded-xl border border-gray-100 bg-white p-4 shadow-sm sm:flex-row">
        <select name="category" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100 sm:w-64">
            <option value="">Semua Kategori</option>
            @foreach($categories as $category)
                <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-lg bg-hmif-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-hmif-700">Filter</button>
        <a href="{{ route('reports.condition') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Reset</a>
    </form>

    <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="hmif-table-scroll">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="border-b border-gray-100 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-5 py-4">Barang</th>
                        <th class="px-5 py-4">Kategori</th>
                        <th class="px-5 py-4">Kondisi</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Lokasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="hmif-break-anywhere px-5 py-4 font-semibold text-gray-900">{{ $item->name }}</td>
                            <td class="px-5 py-4">{{ $item->category }}</td>
                            <td class="px-5 py-4">{{ ['good' => 'Baik', 'fair' => 'Layak Pakai', 'damaged' => 'Rusak'][$item->condition] ?? $item->condition }}</td>
                            <td class="px-5 py-4">{{ ['available' => 'Tersedia', 'borrowed' => 'Dipinjam', 'maintenance' => 'Maintenance'][$item->status] ?? $item->status }}</td>
                            <td class="px-5 py-4">{{ $item->location ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-gray-500">Tidak ada barang sesuai filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
