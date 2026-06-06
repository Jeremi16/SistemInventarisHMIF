@extends('layouts.master')

@section('title', 'Katalog Barang HMIF')

@section('content')
<div class="space-y-6">
    {{-- Header & Search + Filter --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800 sm:text-2xl">Katalog Barang</h2>
            <p class="text-sm text-gray-500 mt-1">Jelajahi dan pinjam barang inventaris HMIF untuk keperluan Anda.</p>
        </div>
    </div>

    {{-- Search Bar & Category Filter --}}
    <div class="rounded-xl border border-gray-100 bg-white p-3 shadow-sm sm:p-4">
        <form method="GET" action="{{ route('catalog.index') }}" data-auto-filter class="grid gap-3 md:grid-cols-[1fr_12rem_12rem_12rem_auto]">
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
            <div>
                <select
                    name="category"
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-hmif-500 bg-white"
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

            <select
                name="status"
                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-hmif-500 bg-white"
            >
                <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>Semua Status</option>
                @foreach(['available' => 'Tersedia', 'borrowed' => 'Dipinjam', 'maintenance' => 'Maintenance'] as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <select
                name="condition"
                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-hmif-500 bg-white"
            >
                <option value="all" {{ request('condition') === 'all' || !request('condition') ? 'selected' : '' }}>Semua Kondisi</option>
                @foreach(['good' => 'Baik', 'fair' => 'Layak Pakai', 'damaged' => 'Rusak'] as $value => $label)
                    <option value="{{ $value }}" {{ request('condition') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            
            @if(request('search') || request('category') || request('status') || request('condition'))
                <a
                    href="{{ route('catalog.index') }}"
                    class="inline-flex items-center justify-center px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors"
                >
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Items Grid --}}
    @if($items->count() > 0)
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 xl:gap-6">
            @foreach($items as $item)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all duration-300 flex flex-col">
                    {{-- Image Placeholder --}}
                    <div class="relative flex h-40 items-center justify-center overflow-hidden border-b border-gray-100 bg-gray-50 sm:h-48">
                        @if($item->photo)
                            <img src="{{ asset('storage/' . $item->photo) }}" alt="Foto {{ $item->name }}" class="h-full w-full object-cover">
                        @else
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        @endif
                        
                        {{-- Status Badge (Absolute) --}}
                        @php
                            $isAvailable = $item->status === 'available' && $item->quantity > 0;
                        @endphp
                        <div class="absolute top-3 right-3">
                            @if($isAvailable)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-green-100 text-green-800 shadow-sm border border-green-200">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                    Available
                                </span>
                            @elseif($item->status === 'maintenance')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-red-100 text-red-800 shadow-sm border border-red-200">
                                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>
                                    Maintenance
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-100 text-amber-800 shadow-sm border border-amber-200">
                                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full mr-1.5"></span>
                                    Borrowed
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Card Content --}}
                    <div class="p-5 flex flex-col flex-1">
                        {{-- Category --}}
                        <span class="text-xs font-bold text-hmif-600 uppercase tracking-wider mb-1">{{ $item->category }}</span>
                        
                        {{-- Title --}}
                        <h3 class="hmif-break-anywhere text-base font-bold text-gray-900 sm:text-lg" title="{{ $item->name }}">{{ $item->name }}</h3>
                        
                        {{-- Stock Info --}}
                        <p class="text-sm text-gray-500 mt-2 flex items-center">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Stok Tersedia: <span class="font-medium text-gray-800 ml-1">{{ $item->quantity }}</span>
                        </p>

                        <div class="mt-auto pt-5">
                            @if($isAvailable)
                                <a href="{{ route('borrowing.request', ['item_id' => $item->id]) }}" class="block w-full text-center px-4 py-2 bg-hmif-600 hover:bg-hmif-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-hmif-500">
                                    Ajukan Pinjam
                                </a>
                            @else
                                <button disabled class="block w-full text-center px-4 py-2 bg-gray-100 text-gray-400 text-sm font-medium rounded-lg cursor-not-allowed border border-gray-200">
                                    Tidak Tersedia
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="flex justify-center mt-8">
            {{ $items->appends(request()->query())->links() }}
        </div>
    @else
        {{-- Empty State with Mock Data to Preview Design --}}
        <div class="bg-hmif-50 border border-hmif-200 text-hmif-800 rounded-xl p-4 mb-6 flex items-start">
            <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-sm">Database saat ini kosong. Menampilkan data mockup agar Anda dapat melihat preview desain Katalog Member.</p>
        </div>
        
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 xl:gap-6">
            @php
                $mocks = [
                    ['name' => 'Proyektor Epson EB-X400', 'cat' => 'Electronics', 'qty' => 2, 'status' => 'available'],
                    ['name' => 'Kabel Roll 50m', 'cat' => 'Event Gear', 'qty' => 0, 'status' => 'borrowed'],
                    ['name' => 'Sound System Portable', 'cat' => 'Electronics', 'qty' => 1, 'status' => 'maintenance'],
                    ['name' => 'Tenda Dome (Kapasitas 4)', 'cat' => 'Event Gear', 'qty' => 5, 'status' => 'available'],
                    ['name' => 'Kertas HVS A4 80gr', 'cat' => 'Office Supplies', 'qty' => 10, 'status' => 'available'],
                    ['name' => 'Kamera DSLR Canon EOS', 'cat' => 'Electronics', 'qty' => 0, 'status' => 'borrowed'],
                ];
            @endphp
            @foreach($mocks as $item)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all duration-300 flex flex-col">
                    <div class="h-48 bg-gray-50 border-b border-gray-100 flex items-center justify-center relative group">
                        <svg class="w-12 h-12 text-gray-300 group-hover:text-hmif-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        
                        <div class="absolute top-3 right-3">
                            @if($item['status'] === 'available')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-green-100 text-green-800 shadow-sm border border-green-200">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span> Available
                                </span>
                            @elseif($item['status'] === 'maintenance')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-red-100 text-red-800 shadow-sm border border-red-200">
                                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span> Maintenance
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-100 text-amber-800 shadow-sm border border-amber-200">
                                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full mr-1.5"></span> Borrowed
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="p-5 flex flex-col flex-1">
                        <span class="text-xs font-bold text-hmif-600 uppercase tracking-wider mb-1">{{ $item['cat'] }}</span>
                        <h3 class="font-bold text-gray-900 text-lg line-clamp-1" title="{{ $item['name'] }}">{{ $item['name'] }}</h3>
                        <p class="text-sm text-gray-500 mt-2 flex items-center">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            Stok Tersedia: <span class="font-medium text-gray-800 ml-1">{{ $item['qty'] }}</span>
                        </p>
                        <div class="mt-auto pt-5">
                            @if($item['status'] === 'available')
                                <a href="{{ route('borrowing.request', ['item_name' => $item['name']]) }}" class="block w-full text-center px-4 py-2 bg-hmif-600 hover:bg-hmif-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-hmif-500">
                                    Ajukan Pinjam
                                </a>
                            @else
                                <button disabled class="block w-full text-center px-4 py-2 bg-gray-50 text-gray-400 text-sm font-medium rounded-lg cursor-not-allowed border border-gray-200">
                                    Tidak Tersedia
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
