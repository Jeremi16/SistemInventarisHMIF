@extends('layouts.master')

@section('title', 'Detail Barang')

@section('content')
@php
    $statusConfig = [
        'available' => ['label' => 'Tersedia', 'class' => 'bg-green-100 text-green-800 border-green-200'],
        'borrowed' => ['label' => 'Dipinjam', 'class' => 'bg-amber-100 text-amber-800 border-amber-200'],
        'maintenance' => ['label' => 'Maintenance', 'class' => 'bg-red-100 text-red-800 border-red-200'],
    ];
    $conditionConfig = [
        'good' => 'Baik',
        'fair' => 'Layak Pakai',
        'damaged' => 'Rusak',
    ];
    $status = $statusConfig[$item->status] ?? $statusConfig['available'];
    $canManageInventory = in_array(strtolower((string) (auth()->user()?->role ?? data_get(session('user', []), 'role'))), ['admin', 'operator'], true);
@endphp

<div class="mx-auto max-w-5xl space-y-6">
    @if(session('item_created'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
            {{ session('item_created') }}
        </div>
    @endif

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.14em] text-hmif-700">Detail Inventaris</p>
            <h2 class="mt-2 text-2xl font-bold text-gray-900">{{ $item->name }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ $item->category }}</p>
        </div>
        <div class="flex flex-col gap-2 sm:flex-row">
            @if($canManageInventory)
                <a href="{{ route('inventory.edit', $item) }}" class="inline-flex items-center justify-center rounded-lg bg-hmif-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-hmif-700">
                    Edit
                </a>
                <a href="{{ route('inventory.qrcode', $item) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    QR Code
                </a>
            @endif
            <a href="{{ route('inventory.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                Kembali ke Katalog
            </a>
        </div>
    </div>

    <section class="grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">
        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex aspect-square items-center justify-center overflow-hidden rounded-xl bg-gray-50 text-gray-300">
                @if($item->photo)
                    <img src="{{ asset('storage/' . $item->photo) }}" alt="Foto {{ $item->name }}" class="h-full w-full object-cover">
                @else
                    <svg class="h-20 w-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                @endif
            </div>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Barang</h3>
                    <p class="mt-1 text-sm text-gray-500">Data stok dan kondisi inventaris.</p>
                </div>
                <span class="inline-flex w-fit rounded-full border px-3 py-1 text-sm font-semibold {{ $status['class'] }}">
                    {{ $status['label'] }}
                </span>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Stok</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $item->quantity }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Kondisi</p>
                    <p class="mt-2 font-semibold text-gray-900">{{ $conditionConfig[$item->condition] ?? $item->condition }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Lokasi</p>
                    <p class="mt-2 font-semibold text-gray-900">{{ $item->location ?? 'Tidak ada lokasi' }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Terakhir Diperbarui</p>
                    <p class="mt-2 font-semibold text-gray-900">{{ $item->updated_at->format('d M Y') }}</p>
                </div>
            </div>

            <div class="mt-6">
                <p class="text-sm font-semibold text-gray-900">Deskripsi</p>
                <p class="mt-2 rounded-lg bg-gray-50 p-4 text-sm leading-6 text-gray-700">
                    {{ $item->description ?: 'Belum ada deskripsi untuk barang ini.' }}
                </p>
            </div>
        </div>
    </section>
</div>
@endsection
