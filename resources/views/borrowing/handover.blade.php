@extends('layouts.master')

@section('title', 'Catat Penyerahan')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <h2 class="text-xl font-bold text-gray-900 sm:text-2xl">Catat Penyerahan Barang</h2>
            <p class="mt-1 text-sm text-gray-500">{{ $borrowing->item_name }} untuk {{ $borrowing->borrower_name }}.</p>
        </div>
        <a href="{{ route('borrowing.show', $borrowing) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Kembali</a>
    </div>

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold">Penyerahan belum dapat disimpan.</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('borrowing.handover.store', $borrowing) }}" enctype="multipart/form-data" class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6">
        @csrf
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="handover_date" class="mb-2 block text-sm font-semibold text-gray-700">Tanggal Penyerahan</label>
                <input id="handover_date" name="handover_date" type="date" value="{{ old('handover_date', now()->toDateString()) }}" required class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            </div>

            <div>
                <label for="handover_condition" class="mb-2 block text-sm font-semibold text-gray-700">Kondisi Saat Diserahkan</label>
                <select id="handover_condition" name="handover_condition" required class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
                    @foreach($conditions as $value => $label)
                        <option value="{{ $value }}" @selected(old('handover_condition') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <label for="handover_photo" class="mb-2 block text-sm font-semibold text-gray-700">Foto Kondisi</label>
                <input id="handover_photo" name="handover_photo" type="file" accept="image/*" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-hmif-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-hmif-700 focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            </div>
        </div>

        <div class="hmif-mobile-actions mt-6 flex-col-reverse sm:justify-end">
            <a href="{{ route('borrowing.show', $borrowing) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Batal</a>
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-hmif-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-hmif-700">Simpan Penyerahan</button>
        </div>
    </form>
</div>
@endsection
