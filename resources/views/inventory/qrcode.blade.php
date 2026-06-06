@extends('layouts.master')

@section('title', 'QR Code Barang')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <h2 class="text-xl font-bold text-gray-900 sm:text-2xl">QR Code Barang</h2>
            <p class="hmif-break-anywhere mt-1 text-sm text-gray-500">Pindai untuk membuka detail {{ $item->name }}.</p>
        </div>
        <a href="{{ route('inventory.show', $item) }}" class="inline-flex w-full items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 sm:w-auto">
            Kembali
        </a>
    </div>

    <div class="rounded-xl border border-gray-100 bg-white p-4 text-center shadow-sm sm:p-6">
        <img src="{{ $qrUrl }}" alt="QR Code {{ $item->name }}" class="mx-auto aspect-square w-full max-w-72 rounded-lg border border-gray-100 bg-white p-3">
        <h3 class="hmif-break-anywhere mt-5 text-lg font-semibold text-gray-900">{{ $item->name }}</h3>
        <p class="mt-1 text-sm text-gray-500">{{ $item->category }} - stok {{ $item->quantity }}</p>

        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-center">
            <a href="{{ route('inventory.qrcode.download', $item) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-lg bg-hmif-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-hmif-700">
                Unduh QR
            </a>
            <a href="{{ route('inventory.show', $item) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                Buka Detail
            </a>
        </div>
    </div>
</div>
@endsection
