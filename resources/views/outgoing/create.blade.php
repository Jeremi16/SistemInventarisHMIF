@extends('layouts.master')

@section('title', 'Catat Barang Keluar')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Catat Barang Keluar</h2>
            <p class="mt-1 text-sm text-gray-500">Stok barang akan berkurang setelah transaksi disimpan.</p>
        </div>
        <a href="{{ route('outgoing.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Kembali</a>
    </div>

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold">Barang keluar belum dapat disimpan.</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('outgoing.store') }}" enctype="multipart/form-data" class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        @csrf
        <div class="grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="item_id" class="mb-2 block text-sm font-semibold text-gray-700">Barang</label>
                <select id="item_id" name="item_id" required class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
                    <option value="">Pilih barang</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" @selected(old('item_id') == $item->id)>{{ $item->name }} - stok {{ $item->quantity }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="reason" class="mb-2 block text-sm font-semibold text-gray-700">Alasan</label>
                <select id="reason" name="reason" required class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
                    @foreach($reasons as $reason)
                        <option value="{{ $reason }}" @selected(old('reason') === $reason)>{{ ucfirst($reason) }}</option>
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
                <label for="documentation_file" class="mb-2 block text-sm font-semibold text-gray-700">Dokumentasi Pendukung</label>
                <input id="documentation_file" name="documentation_file" type="file" accept=".pdf,image/*" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-hmif-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-hmif-700 focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            </div>

            <div class="sm:col-span-2">
                <label for="notes" class="mb-2 block text-sm font-semibold text-gray-700">Catatan</label>
                <textarea id="notes" name="notes" rows="4" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="{{ route('outgoing.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Batal</a>
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-hmif-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-hmif-700">Simpan</button>
        </div>
    </form>
</div>
@endsection
