@extends('layouts.master')

@section('title', 'Katalog Inventaris')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    {{-- Header & Search + Filter --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Katalog Inventaris</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola dan lihat semua barang inventaris HMIF</p>
        </div>
        <a
            href="{{ route('inventory.create') }}"
            class="inline-flex items-center justify-center px-4 py-2.5 bg-hmif-600 hover:bg-hmif-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-hmif-500"
        >
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Barang Baru
        </a>
    </div>

    {{-- Search Bar & Category Filter --}}
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
        <form method="GET" action="{{ route('inventory.index') }}" class="flex flex-col sm:flex-row gap-3">
            {{-- Search Input --}}
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama barang..."
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-hmif-500 focus:border-transparent placeholder-gray-400"
                >
            </div>

            {{-- Category Filter --}}
            <div class="sm:w-48">
                <select
                    name="category"
                    onchange="this.form.submit()"
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-hmif-500 focus:border-transparent bg-white appearance-none cursor-pointer"
                >
                    <option value="all" {{ request('category') === 'all' || !request('category') ? 'selected' : '' }}>
                        Semua Kategori
                    </option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Reset Button --}}
            @if(request('search') || request('category'))
                <a
                    href="{{ route('inventory.index') }}"
                    class="inline-flex items-center justify-center px-4 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Results Count --}}
    <p class="text-sm text-gray-500">
        Menampilkan {{ $items->total() }} barang
        @if(request('search') || request('category'))
            dengan filter aktif
        @endif
    </p>

    {{-- Items Grid --}}
    @if($items->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($items as $item)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
                    {{-- Image Placeholder --}}
                    <div class="h-36 bg-gray-100 flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>

                    {{-- Card Content --}}
                    <div class="p-4 space-y-3">
                        <div>
                            <h3 class="font-semibold text-gray-800 text-sm truncate">{{ $item->name }}</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $item->location ?? 'Tidak ada lokasi' }}</p>
                        </div>

                        {{-- Category Badge --}}
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-hmif-50 text-hmif-700">
                            {{ $item->category }}
                        </span>

                        {{-- Status & Quantity --}}
                        <div class="flex items-center justify-between">
                            @php
                                $statusConfig = [
                                    'available' => ['label' => 'Tersedia', 'bg' => 'bg-green-100', 'text' => 'text-green-700', 'dot' => 'bg-green-500'],
                                    'borrowed' => ['label' => 'Dipinjam', 'bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'dot' => 'bg-amber-500'],
                                    'maintenance' => ['label' => 'Maintenance', 'bg' => 'bg-red-100', 'text' => 'text-red-700', 'dot' => 'bg-red-500'],
                                ];
                                $status = $statusConfig[$item->status] ?? $statusConfig['available'];
                            @endphp
                            <span class="inline-flex items-center gap-1.5 {{ $status['bg'] }} {{ $status['text'] }} px-2 py-1 rounded-md text-xs font-medium">
                                <span class="w-1.5 h-1.5 {{ $status['dot'] }} rounded-full"></span>
                                {{ $status['label'] }}
                            </span>
                            <span class="text-xs text-gray-400">Stok: {{ $item->quantity }}</span>
                        </div>

                        {{-- Action Button --}}
                        <a href="{{ route('inventory.show', $item) }}" class="block w-full text-center px-3 py-2 bg-hmif-600 hover:bg-hmif-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="flex justify-center mt-6">
            {{ $items->appends(request()->query())->links() }}
        </div>
    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-700">Tidak ada barang ditemukan</h3>
            <p class="text-sm text-gray-500 mt-1">Coba ubah kata kunci pencarian atau filter kategori</p>
            <a href="{{ route('inventory.index') }}" class="inline-flex items-center mt-4 px-4 py-2 bg-hmif-600 hover:bg-hmif-700 text-white text-sm font-medium rounded-lg transition-colors">
                Reset Filter
            </a>
        </div>
    @endif
</div>
@endsection
