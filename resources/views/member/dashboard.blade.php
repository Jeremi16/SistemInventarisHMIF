@extends('layouts.master')

@section('title', 'Dashboard Anggota')

@section('content')
@php
    $statusStyles = [
        'Menunggu' => 'bg-amber-100 text-amber-800 border-amber-200',
        'Disetujui' => 'bg-blue-100 text-blue-800 border-blue-200',
        'Diterima' => 'bg-green-100 text-green-800 border-green-200',
        'Dikembalikan' => 'bg-gray-100 text-gray-700 border-gray-200',
        'Ditolak' => 'bg-red-100 text-red-800 border-red-200',
        'Terlambat' => 'bg-red-100 text-red-800 border-red-200',
    ];

    $statusDots = [
        'Menunggu' => 'bg-amber-500',
        'Disetujui' => 'bg-blue-500',
        'Diterima' => 'bg-green-500',
        'Dikembalikan' => 'bg-gray-500',
        'Ditolak' => 'bg-red-500',
        'Terlambat' => 'bg-red-500',
    ];

    $latestRequest = $borrowings[0] ?? null;
    $requestFlow = ['Menunggu', 'Disetujui', 'Diterima', 'Dikembalikan'];
    $latestStatusIndex = $latestRequest ? array_search($latestRequest['status'], $requestFlow, true) : false;

    $catalogItems = $availableItems->isNotEmpty() ? $availableItems : collect($fallbackItems);
@endphp

<div class="space-y-6">
    <section class="overflow-hidden rounded-xl bg-[#123829] text-white shadow-sm">
        <div class="grid gap-6 p-6 lg:grid-cols-[1fr_20rem] lg:p-8">
            <div>
                <p class="inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-lime-100 ring-1 ring-white/15">Portal Anggota</p>
                <h2 class="mt-5 text-2xl font-bold tracking-tight sm:text-3xl">Halo, {{ $member['name'] }}</h2>
                
                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('catalog.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#cddc39] px-4 py-2.5 text-sm font-semibold text-[#153b2d] transition hover:bg-[#d9e85a]">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.2-5.2M10.8 18a7.2 7.2 0 1 1 0-14.4 7.2 7.2 0 0 1 0 14.4Z"/>
                        </svg>
                        Cari Barang
                    </a>
                    <a href="{{ route('borrowing.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-white/10 px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-white/20 transition hover:bg-white/15">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.5L19 9.5V19a2 2 0 0 1-2 2Z"/>
                        </svg>
                        Lihat Status
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-gray-500">Barang Tersedia</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['available_items'] ?: 8 }}</p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-green-100 text-green-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pengajuan Aktif</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['active_requests'] }}</p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-blue-100 text-blue-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6m-6 4h6m-7 4h8m-9 8h10a2 2 0 0 0 2-2V7.5L14.5 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2Z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-gray-500">Menunggu Admin</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['pending_requests'] }}</p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-gray-500">Jatuh Tempo</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ $stats['nearest_due'] }}</p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-red-100 text-red-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.14em] text-hmif-700">Status Permintaan Terkini</p>
                @if($latestRequest)
                    <div class="mt-3 flex flex-wrap items-center gap-3">
                        <h3 class="text-xl font-bold text-gray-900">{{ $latestRequest['item'] }}</h3>
                        <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-sm font-semibold {{ $statusStyles[$latestRequest['status']] ?? 'bg-gray-100 text-gray-700 border-gray-200' }}">
                            <span class="h-2 w-2 rounded-full {{ $statusDots[$latestRequest['status']] ?? 'bg-gray-500' }}"></span>
                            {{ $latestRequest['status'] }}
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">{{ $latestRequest['date'] }} - {{ $latestRequest['due'] }}</p>
                    <p class="mt-3 max-w-3xl rounded-lg bg-gray-50 px-4 py-3 text-sm leading-6 text-gray-700">{{ $latestRequest['note'] }}</p>
                @else
                    <h3 class="mt-3 text-xl font-bold text-gray-900">Belum ada permintaan aktif</h3>
                    <p class="mt-2 text-sm text-gray-500">Pilih barang dari katalog untuk mulai mengajukan peminjaman.</p>
                @endif
            </div>

            <div class="flex flex-col gap-3 sm:flex-row lg:flex-col">
                <a href="{{ route('borrowing.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    Lihat Semua Status
                </a>
            </div>
        </div>

        @if($latestRequest)
            <div class="mt-6 grid gap-3 md:grid-cols-4">
                @foreach($requestFlow as $index => $flowStatus)
                    @php
                        $isComplete = $latestStatusIndex !== false && $index <= $latestStatusIndex;
                    @endphp
                    <div class="rounded-lg border {{ $isComplete ? 'border-hmif-200 bg-hmif-50' : 'border-gray-100 bg-gray-50' }} p-3">
                        <div class="flex items-center gap-3">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold {{ $isComplete ? 'bg-hmif-600 text-white' : 'bg-white text-gray-400 ring-1 ring-gray-200' }}">
                                {{ $index + 1 }}
                            </span>
                            <span class="text-sm font-semibold {{ $isComplete ? 'text-hmif-900' : 'text-gray-500' }}">{{ $flowStatus }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.4fr_0.9fr]">
        <div class="rounded-xl border border-gray-100 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-gray-100 p-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Peminjaman Saya</h3>
                    <p class="mt-1 text-sm text-gray-500">Status terkini dan catatan admin/operator.</p>
                </div>
                <a href="{{ route('catalog.index') }}" class="inline-flex items-center justify-center rounded-lg bg-hmif-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-hmif-700">Ajukan Baru</a>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($borrowings as $borrowing)
                    <article class="grid gap-4 p-5 md:grid-cols-[1fr_auto] md:items-center">
                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <h4 class="font-semibold text-gray-900">{{ $borrowing['item'] }}</h4>
                                <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusStyles[$borrowing['status']] ?? 'bg-gray-100 text-gray-700 border-gray-200' }}">
                                    {{ $borrowing['status'] }}
                                </span>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Pinjam {{ $borrowing['date'] }} - kembali {{ $borrowing['due'] }}</p>
                            <p class="mt-2 text-sm text-gray-700">{{ $borrowing['note'] }}</p>
                        </div>
                        <a href="{{ route('borrowing.show', $borrowing['id']) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                            Detail
                        </a>
                    </article>
                @empty
                    <div class="p-8 text-center text-sm text-gray-500">
                        Belum ada peminjaman yang diajukan.
                    </div>
                @endforelse
            </div>
        </div>

        <aside class="space-y-6">
            <div class="theme-note-panel rounded-xl border border-[#d7e78a] bg-[#fbfde8] p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#cddc39] text-[#153b2d]">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M11 3.75h2L21.25 18a2 2 0 0 1-1.73 3H4.48a2 2 0 0 1-1.73-3L11 3.75Z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="theme-note-title font-semibold text-[#153b2d]">Catatan Peminjaman</h3>
                        <ul class="theme-note-text mt-3 space-y-2 text-sm leading-6 text-[#315343]">
                            <li>Konfirmasi ke WhatsApp setelah formulir dikirim.</li>
                            <li>Cek catatan status secara berkala sebelum mengambil barang.</li>
                            <li>Barang rusak atau hilang wajib diganti sesuai ketentuan.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
                <h3 class="font-semibold text-gray-900">Alur Singkat</h3>
                <div class="mt-4 space-y-4">
                    @foreach(['Pilih barang dari katalog', 'Isi durasi dan keperluan', 'Konfirmasi via WhatsApp', 'Tunggu persetujuan admin', 'Kembalikan dan cek kondisi'] as $index => $step)
                        <div class="flex gap-3">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-hmif-50 text-xs font-bold text-hmif-700">{{ $index + 1 }}</span>
                            <p class="pt-1 text-sm text-gray-600">{{ $step }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>
    </section>

    <section class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Barang Siap Dipinjam</h3>
                <p class="mt-1 text-sm text-gray-500">Pilihan cepat dari katalog inventaris HMIF.</p>
            </div>
            <a href="{{ route('catalog.index') }}" class="text-sm font-semibold text-hmif-700 hover:text-hmif-800">Lihat katalog lengkap</a>
        </div>

        <div class="mt-5 grid gap-4 md:grid-cols-3 xl:grid-cols-4">
            @foreach($catalogItems as $item)
                <article class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <div class="mb-4 flex h-28 items-center justify-center rounded-lg bg-white text-gray-300">
                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M20 7l-8-4-8 4m16 0-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <p class="text-xs font-bold uppercase tracking-wide text-hmif-700">{{ data_get($item, 'category') }}</p>
                    <h4 class="mt-1 truncate font-semibold text-gray-900">{{ data_get($item, 'name') }}</h4>
                    <div class="mt-3 flex items-center justify-between text-sm text-gray-500">
                        <span>Stok {{ data_get($item, 'quantity') }}</span>
                        <span>{{ data_get($item, 'location', 'HMIF') }}</span>
                    </div>
                    <a href="{{ data_get($item, 'id') ? route('borrowing.request', ['item_id' => data_get($item, 'id')]) : route('borrowing.request', ['item_name' => data_get($item, 'name')]) }}" class="mt-4 inline-flex w-full items-center justify-center rounded-lg bg-hmif-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-hmif-700">
                        Ajukan Pinjam
                    </a>
                </article>
            @endforeach
        </div>
    </section>
</div>
@endsection
