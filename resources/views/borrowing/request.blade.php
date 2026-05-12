@extends('layouts.master')

@section('title', 'Form Pengajuan Peminjaman')

@section('content')
@php
    $success = session('borrowing_success');
    $hasItem = filled($itemName);
@endphp

<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg p-6 md:p-8 shadow-sm border border-gray-100">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Formulir Pengajuan Peminjaman</h2>
            <p class="text-gray-500 mt-2">Lengkapi detail peminjaman barang inventaris HMIF.</p>
        </div>

        @if($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="font-semibold">Pengajuan belum dapat dikirim.</p>
                <ul class="mt-2 list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('borrowing.store') }}" method="POST" class="space-y-6">
            @csrf

            @if($item)
                <input type="hidden" name="item_id" value="{{ $item->id }}">
            @endif

            <div>
                <label for="item_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Barang</label>
                <input
                    type="text"
                    id="item_name"
                    name="item_name"
                    value="{{ old('item_name', $itemName ?? '') }}"
                    readonly
                    class="w-full rounded-lg border-gray-300 bg-gray-50 text-gray-600 cursor-not-allowed px-4 py-2 focus:ring-hmif-500 focus:border-hmif-500"
                    placeholder="Pilih barang dari katalog"
                >
                <p class="text-xs text-gray-400 mt-1">Barang otomatis terisi dari katalog dan tidak dapat diubah di formulir ini.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="borrower_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Peminjam</label>
                    <input
                        type="text"
                        id="borrower_name"
                        value="{{ $borrower['name'] }}"
                        readonly
                        class="w-full rounded-lg border-gray-300 bg-gray-50 text-gray-600 cursor-not-allowed px-4 py-2 focus:ring-hmif-500 focus:border-hmif-500"
                    >
                </div>
                <div>
                    <label for="borrower_nim" class="block text-sm font-medium text-gray-700 mb-1">NIM Peminjam</label>
                    <input
                        type="text"
                        id="borrower_nim"
                        value="{{ $borrower['nim'] }}"
                        readonly
                        class="w-full rounded-lg border-gray-300 bg-gray-50 text-gray-600 cursor-not-allowed px-4 py-2 focus:ring-hmif-500 focus:border-hmif-500"
                    >
                </div>
            </div>
            <p class="-mt-4 text-xs text-gray-400">Nama dan NIM diambil dari sesi login agar data peminjam tidak dapat diisi manual.</p>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Durasi Pinjam</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-xs font-medium text-gray-500 mb-1">Tanggal Mulai</label>
                        <input
                            type="date"
                            id="start_date"
                            name="start_date"
                            value="{{ old('start_date') }}"
                            required
                            class="w-full rounded-lg border-gray-300 px-4 py-2 focus:ring-hmif-500 focus:border-hmif-500"
                        >
                    </div>
                    <div>
                        <label for="end_date" class="block text-xs font-medium text-gray-500 mb-1">Tanggal Pengembalian</label>
                        <input
                            type="date"
                            id="end_date"
                            name="end_date"
                            value="{{ old('end_date') }}"
                            required
                            class="w-full rounded-lg border-gray-300 px-4 py-2 focus:ring-hmif-500 focus:border-hmif-500"
                        >
                    </div>
                </div>
            </div>

            <div>
                <label for="purpose" class="block text-sm font-medium text-gray-700 mb-1">Keperluan</label>
                <textarea
                    id="purpose"
                    name="purpose"
                    rows="4"
                    required
                    class="w-full rounded-lg border-gray-300 px-4 py-2 focus:ring-hmif-500 focus:border-hmif-500"
                    placeholder="Contoh: Digunakan untuk kegiatan rapat kerja HMIF."
                >{{ old('purpose') }}</textarea>
            </div>

            <div class="border border-hmif-200 bg-hmif-50 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-lg bg-white text-hmif-700 border border-hmif-100">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-hmif-900">Syarat & Ketentuan Peminjaman</h3>
                        <ul class="mt-3 list-disc list-inside space-y-1 text-sm text-hmif-900">
                            <li>Peminjam wajib mengecek catatan dan status peminjaman secara berkala.</li>
                            <li>Durasi pinjam wajib sesuai tanggal pengajuan dan persetujuan admin/operator.</li>
                            <li>Keterlambatan pengembalian dapat dikenakan denda sesuai ketentuan HMIF.</li>
                            <li>Kerusakan, kehilangan, atau perubahan kondisi barang menjadi tanggung jawab peminjam.</li>
                            <li>Pengajuan harus dikonfirmasi ke WhatsApp setelah formulir dikirim.</li>
                        </ul>

                        <label for="terms_accepted" class="mt-4 flex items-start gap-3 text-sm text-hmif-900">
                            <input
                                type="checkbox"
                                id="terms_accepted"
                                name="terms_accepted"
                                value="1"
                                required
                                class="mt-1 rounded border-hmif-300 text-hmif-600 focus:ring-hmif-500"
                            >
                            <span>Saya telah membaca dan menyetujui syarat serta ketentuan peminjaman.</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex flex-col sm:flex-row sm:justify-end gap-3">
                <a href="{{ route('catalog.index') }}" class="inline-flex items-center justify-center px-5 py-3 border border-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Kembali ke Katalog
                </a>
                <button
                    type="submit"
                    @disabled(! $hasItem)
                    class="inline-flex items-center justify-center px-6 py-3 bg-hmif-600 hover:bg-hmif-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-hmif-500 shadow-sm"
                >
                    Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

@if($success)
    <div id="borrowing-success-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 px-4" role="dialog" aria-modal="true" aria-labelledby="borrowing-success-title">
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
            <div class="flex items-start gap-4">
                <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-lg bg-green-100 text-green-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75 10 18.25 19.5 5.75"/>
                    </svg>
                </div>
                <div>
                    <h3 id="borrowing-success-title" class="text-lg font-semibold text-gray-900">Pengajuan berhasil dikirim</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Lanjutkan konfirmasi peminjaman {{ $success['item'] }} ke WhatsApp.
                    </p>
                </div>
            </div>

            <div class="mt-6 rounded-lg bg-gray-50 p-4 text-sm text-gray-700">
                <p class="font-medium text-gray-900">Pesan otomatis:</p>
                <p class="mt-1">Halo, saya {{ $success['name'] }} NIM {{ $success['nim'] }} ingin konfirmasi peminjaman {{ $success['item'] }}</p>
            </div>

            <div class="mt-6 flex flex-col sm:flex-row gap-3 sm:justify-end">
                <button type="button" onclick="closeBorrowingSuccessModal()" class="inline-flex items-center justify-center px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Tutup
                </button>
                <a
                    href="{{ $success['whatsapp_url'] }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center justify-center px-4 py-2 bg-[#25D366] hover:bg-[#128C7E] text-white font-medium rounded-lg transition-colors"
                >
                    Redirect ke WhatsApp
                </a>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
    function closeBorrowingSuccessModal() {
        const modal = document.getElementById('borrowing-success-modal');

        if (modal) {
            modal.classList.add('hidden');
        }
    }
</script>
@endpush
