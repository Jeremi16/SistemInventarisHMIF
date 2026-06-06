@extends('layouts.master')

@section('title', 'Histori Transaksi')

@section('content')
<div class="space-y-6">
    <div class="min-w-0">
        <h2 class="text-xl font-bold text-gray-900 sm:text-2xl">Histori Barang Masuk/Keluar</h2>
        <p class="mt-1 text-sm text-gray-500">Riwayat transaksi inventaris untuk audit organisasi.</p>
    </div>

    <form method="GET" action="{{ route('reports.history') }}" data-auto-filter class="grid gap-3 rounded-xl border border-gray-100 bg-white p-4 shadow-sm md:grid-cols-5">
        <select name="source" class="rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            <option value="">Sumber Masuk</option>
            @foreach($sources as $source)
                <option value="{{ $source }}" @selected(request('source') === $source)>{{ ucfirst($source) }}</option>
            @endforeach
        </select>
        <select name="reason" class="rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            <option value="">Alasan Keluar</option>
            @foreach($reasons as $reason)
                <option value="{{ $reason }}" @selected(request('reason') === $reason)>{{ ucfirst($reason) }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
        <div class="flex flex-col gap-2 sm:flex-row">
            <button type="submit" class="flex-1 rounded-lg bg-hmif-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-hmif-700">Filter</button>
            <a href="{{ route('reports.history') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Reset</a>
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="hmif-table-scroll">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="border-b border-gray-100 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-5 py-4">Tanggal</th>
                        <th class="px-5 py-4">Jenis</th>
                        <th class="px-5 py-4">Barang</th>
                        <th class="px-5 py-4">Jumlah</th>
                        <th class="px-5 py-4">Detail</th>
                        <th class="px-5 py-4">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transactions as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-5 py-4">{{ $transaction['date'] }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $transaction['type'] === 'Masuk' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $transaction['type'] }}
                                </span>
                            </td>
                            <td class="hmif-break-anywhere px-5 py-4 font-semibold text-gray-900">{{ $transaction['item_name'] }}</td>
                            <td class="px-5 py-4">{{ $transaction['quantity'] }}</td>
                            <td class="hmif-break-anywhere px-5 py-4">{{ $transaction['detail'] }}</td>
                            <td class="hmif-break-anywhere px-5 py-4">{{ $transaction['notes'] ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-500">Belum ada histori transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex flex-col gap-3 border-t border-gray-100 px-4 py-4 text-sm text-gray-500 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <span>Halaman {{ $page }} dari {{ max(1, (int) ceil($total / $perPage)) }}</span>
            <div class="flex flex-col gap-2 sm:flex-row">
                @if($page > 1)
                    <a href="{{ route('reports.history', array_merge(request()->query(), ['page' => $page - 1])) }}" class="rounded-lg border border-gray-200 px-3 py-2 font-semibold text-gray-700 hover:bg-gray-50">Sebelumnya</a>
                @endif
                @if($page * $perPage < $total)
                    <a href="{{ route('reports.history', array_merge(request()->query(), ['page' => $page + 1])) }}" class="rounded-lg border border-gray-200 px-3 py-2 font-semibold text-gray-700 hover:bg-gray-50">Berikutnya</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
