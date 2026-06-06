@extends('layouts.master')

@section('title', 'Barang Masuk')

@section('content')
<div class="space-y-6">
    @if(session('incoming_created'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
            {{ session('incoming_created') }}
        </div>
    @endif

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Barang Masuk</h2>
        </div>
        <a href="{{ route('incoming.create') }}" class="inline-flex items-center justify-center rounded-lg bg-hmif-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-hmif-700">
            Catat Barang Masuk
        </a>
    </div>

    <form method="GET" action="{{ route('incoming.index') }}" class="grid gap-3 rounded-xl border border-gray-100 bg-white p-4 shadow-sm md:grid-cols-4">
        <select name="source" class="rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            <option value="">Semua Sumber</option>
            @foreach($sources as $source)
                <option value="{{ $source }}" @selected(request('source') === $source)>{{ ucfirst($source) }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
        <div class="flex gap-2">
            <button type="submit" class="flex-1 rounded-lg bg-hmif-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-hmif-700">Filter</button>
            <a href="{{ route('incoming.index') }}" class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Reset</a>
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="border-b border-gray-100 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-5 py-4">Tanggal</th>
                        <th class="px-5 py-4">Barang</th>
                        <th class="px-5 py-4">Sumber</th>
                        <th class="px-5 py-4">Jumlah</th>
                        <th class="px-5 py-4">Bukti</th>
                        <th class="px-5 py-4">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($incomings as $incoming)
                        <tr class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-5 py-4">{{ $incoming->date->format('d M Y') }}</td>
                            <td class="px-5 py-4 font-semibold text-gray-900">{{ $incoming->item?->name ?? '-' }}</td>
                            <td class="px-5 py-4">{{ ucfirst($incoming->source) }}</td>
                            <td class="px-5 py-4 font-semibold text-gray-900">{{ $incoming->quantity }}</td>
                            <td class="px-5 py-4">
                                @if($incoming->proof_file)
                                    <a href="{{ asset('storage/' . $incoming->proof_file) }}" target="_blank" rel="noopener noreferrer" class="text-sm font-semibold text-hmif-700 hover:text-hmif-800">Lihat</a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <p class="max-w-64 whitespace-normal break-words leading-6">
                                    {{ $incoming->notes ? \Illuminate\Support\Str::limit($incoming->notes, 100) : '-' }}
                                </p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-500">Belum ada catatan barang masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-100 px-5 py-4">
            {{ $incomings->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
