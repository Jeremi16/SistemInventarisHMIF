@extends('layouts.master')

@section('title', 'Catat Pengembalian')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Catat Pengembalian Barang</h2>
            <p class="mt-1 text-sm text-gray-500">{{ $borrowing->item_name }} dari {{ $borrowing->borrower_name }}.</p>
        </div>
        <a href="{{ route('borrowing.show', $borrowing) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Kembali</a>
    </div>

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold">Pengembalian belum dapat disimpan.</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('borrowing.return.store', $borrowing) }}" enctype="multipart/form-data" class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        @csrf
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="return_date" class="mb-2 block text-sm font-semibold text-gray-700">Tanggal Pengembalian</label>
                <input id="return_date" name="return_date" type="date" value="{{ old('return_date', now()->toDateString()) }}" required class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            </div>

            <div>
                <label for="return_condition" class="mb-2 block text-sm font-semibold text-gray-700">Kondisi Saat Dikembalikan</label>
                <select id="return_condition" name="return_condition" required class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
                    @foreach($conditions + ['lost' => 'Hilang'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('return_condition') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <label for="return_photo" class="mb-2 block text-sm font-semibold text-gray-700">Foto Kondisi</label>
                <input id="return_photo" name="return_photo" type="file" accept="image/*" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-hmif-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-hmif-700 focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            </div>

            <div class="sm:col-span-2">
                <label for="damage_description" class="mb-2 block text-sm font-semibold text-gray-700">Deskripsi Kerusakan / Kehilangan</label>
                <textarea id="damage_description" name="damage_description" rows="4" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100" placeholder="Wajib diisi jika barang rusak atau hilang.">{{ old('damage_description') }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="{{ route('borrowing.show', $borrowing) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Batal</a>
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-hmif-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-hmif-700">Simpan Pengembalian</button>
        </div>
    </form>
</div>
@endsection
