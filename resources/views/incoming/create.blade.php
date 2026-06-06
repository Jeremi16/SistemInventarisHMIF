@extends('layouts.master')

@section('title', 'Catat Barang Masuk')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <h2 class="text-xl font-bold text-gray-900 sm:text-2xl">Catat Barang Masuk</h2>
            <p class="mt-1 text-sm text-gray-500">Stok barang akan bertambah setelah transaksi disimpan.</p>
        </div>
        <a href="{{ route('incoming.index') }}" class="inline-flex w-full items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 sm:w-auto">Kembali</a>
    </div>

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold">Barang masuk belum dapat disimpan.</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('incoming.store') }}" enctype="multipart/form-data" class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6">
        @csrf
        <div class="grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="item_name" class="mb-2 block text-sm font-semibold text-gray-700">Barang</label>
                <input id="item_name" name="item_name" type="text" value="{{ old('item_name') }}" required maxlength="255" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100" placeholder="Masukkan nama barang">
            </div>

            <div>
                <label for="source" class="mb-2 block text-sm font-semibold text-gray-700">Sumber</label>
                <select id="source" name="source" required class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
                    @foreach($sources as $source)
                        <option value="{{ $source }}" @selected(old('source') === $source)>{{ ucfirst($source) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="date" class="mb-2 block text-sm font-semibold text-gray-700">Tanggal</label>
                <input id="date" name="date" type="date" value="{{ old('date', now()->toDateString()) }}" required class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            </div>

            <div>
                <label for="quantity" class="mb-2 block text-sm font-semibold text-gray-700">Jumlah</label>
                <input id="quantity" name="quantity" type="number" min="1" value="{{ old('quantity', 1) }}" required class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            </div>

            <div>
                <label for="proof_file" class="mb-2 block text-sm font-semibold text-gray-700">Bukti Pengadaan</label>
                <input id="proof_file" name="proof_file" type="file" accept=".pdf,image/*" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-hmif-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-hmif-700 focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            </div>

            <div class="sm:col-span-2">
                <label for="notes" class="mb-2 block text-sm font-semibold text-gray-700">Catatan</label>
                <textarea id="notes" name="notes" rows="4" maxlength="100" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">{{ old('notes') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Maksimal 100 karakter.</p>
            </div>
        </div>

        <div class="hmif-mobile-actions mt-6 sm:justify-end">
            <a href="{{ route('incoming.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Batal</a>
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-hmif-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-hmif-700">Simpan</button>
        </div>
    </form>
</div>
@endsection
