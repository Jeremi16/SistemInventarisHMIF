@extends('layouts.master')

@section('title', 'Perpanjangan Pinjaman')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <h2 class="text-xl font-bold text-gray-900 sm:text-2xl">Ajukan Perpanjangan</h2>
            <p class="mt-1 text-sm text-gray-500">{{ $borrowing->item_name }} jatuh tempo {{ $borrowing->endDateTime()?->format('d M Y H:i:s') }}.</p>
        </div>
        <a href="{{ route('borrowing.show', $borrowing) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Kembali</a>
    </div>

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold">Perpanjangan belum dapat diajukan.</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('borrowing.extension.request', $borrowing) }}" class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6">
        @csrf
        <div class="grid gap-5">
            <div>
                <label for="extension_new_date" class="mb-2 block text-sm font-semibold text-gray-700">Tanggal Kembali Baru</label>
                <input id="extension_new_date" name="extension_new_date" type="date" value="{{ old('extension_new_date') }}" required class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            </div>

            <div>
                <label for="extension_reason" class="mb-2 block text-sm font-semibold text-gray-700">Alasan Perpanjangan</label>
                <textarea id="extension_reason" name="extension_reason" rows="4" required class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">{{ old('extension_reason') }}</textarea>
            </div>
        </div>

        <div class="hmif-mobile-actions mt-6 flex-col-reverse sm:justify-end">
            <a href="{{ route('borrowing.show', $borrowing) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Batal</a>
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-hmif-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-hmif-700">Ajukan Perpanjangan</button>
        </div>
    </form>
</div>
@endsection
