@extends('layouts.master')

@section('title', 'Barang Keluar')

@section('content')
<div class="space-y-6">
    @if(session('outgoing_created'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
            {{ session('outgoing_created') }}
        </div>
    @endif

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Barang Keluar</h2>
        </div>
        <a href="{{ route('outgoing.create') }}" class="inline-flex items-center justify-center rounded-lg bg-hmif-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-hmif-700">
            Catat Barang Keluar
        </a>
    </div>

    <form method="GET" action="{{ route('outgoing.index') }}" class="grid gap-3 rounded-xl border border-gray-100 bg-white p-4 shadow-sm md:grid-cols-4">
        <select name="reason" class="rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            <option value="">Semua Alasan</option>
            @foreach($reasons as $reason)
                <option value="{{ $reason }}" @selected(request('reason') === $reason)>{{ ucfirst($reason) }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
        <div class="flex gap-2">
            <button type="submit" class="flex-1 rounded-lg bg-hmif-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-hmif-700">Filter</button>
            <a href="{{ route('outgoing.index') }}" class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Reset</a>
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="border-b border-gray-100 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-5 py-4">Tanggal</th>
                        <th class="px-5 py-4">Barang</th>
                        <th class="px-5 py-4">Alasan</th>
                        <th class="px-5 py-4">Jumlah</th>
                        <th class="px-5 py-4">Dokumentasi</th>
                        <th class="px-5 py-4">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($outgoings as $outgoing)
                        <tr class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-5 py-4">{{ $outgoing->date->format('d M Y') }}</td>
                            <td class="px-5 py-4 font-semibold text-gray-900">{{ $outgoing->item?->name ?? '-' }}</td>
                            <td class="px-5 py-4">{{ ucfirst($outgoing->reason) }}</td>
                            <td class="px-5 py-4 font-semibold text-gray-900">{{ $outgoing->quantity }}</td>
                            <td class="px-5 py-4">
                                @if($outgoing->documentation_file)
                                    <a href="{{ asset('storage/' . $outgoing->documentation_file) }}" target="_blank" rel="noopener noreferrer" class="text-sm font-semibold text-hmif-700 hover:text-hmif-800">Lihat</a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">{{ $outgoing->notes ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-500">Belum ada catatan barang keluar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-100 px-5 py-4">
            {{ $outgoings->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
