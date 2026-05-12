@extends('layouts.master')

@section('title', 'Detail Peminjaman')

@section('content')
@php
    $statusConfig = [
        'pending' => ['label' => 'Menunggu', 'class' => 'bg-yellow-100 text-yellow-800 border-yellow-200'],
        'approved' => ['label' => 'Disetujui', 'class' => 'bg-blue-100 text-blue-800 border-blue-200'],
        'rejected' => ['label' => 'Ditolak', 'class' => 'bg-red-100 text-red-800 border-red-200'],
        'borrowed' => ['label' => 'Dipinjam', 'class' => 'bg-indigo-100 text-indigo-800 border-indigo-200'],
        'returned' => ['label' => 'Dikembalikan', 'class' => 'bg-green-100 text-green-800 border-green-200'],
        'overdue' => ['label' => 'Terlambat', 'class' => 'bg-red-100 text-red-800 border-red-200'],
    ];
    $status = $statusConfig[$borrowing->status] ?? $statusConfig['pending'];
@endphp

<div class="mx-auto max-w-5xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.14em] text-hmif-700">Detail Peminjaman</p>
            <h2 class="mt-2 text-2xl font-bold text-gray-900">{{ $borrowing->item_name }}</h2>
            <p class="mt-1 text-sm text-gray-500">Pengajuan #{{ $borrowing->id }} oleh {{ $borrowing->borrower_name }}</p>
        </div>
        <a href="{{ $isAdmin ? route('borrowing.index') : route('member.dashboard') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
            Kembali
        </a>
    </div>

    <section class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Status Permintaan</h3>
                    <p class="mt-1 text-sm text-gray-500">Status ini diperbarui oleh admin.</p>
                </div>
                <span class="inline-flex w-fit items-center rounded-full border px-3 py-1 text-sm font-semibold {{ $status['class'] }}">
                    {{ $status['label'] }}
                </span>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Tanggal Pinjam</p>
                    <p class="mt-2 font-semibold text-gray-900">{{ $borrowing->start_date->format('d M Y') }}</p>
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Tanggal Kembali</p>
                    <p class="mt-2 font-semibold text-gray-900">{{ $borrowing->end_date->format('d M Y') }}</p>
                </div>
            </div>

            <div class="mt-6">
                <p class="text-sm font-semibold text-gray-900">Keperluan</p>
                <p class="mt-2 rounded-lg bg-gray-50 p-4 text-sm leading-6 text-gray-700">{{ $borrowing->purpose }}</p>
            </div>

            <div class="mt-6">
                <p class="text-sm font-semibold text-gray-900">Catatan Admin</p>
                <p class="mt-2 rounded-lg bg-hmif-50 p-4 text-sm leading-6 text-hmif-900">
                    {{ $borrowing->admin_note ?: 'Belum ada catatan dari admin.' }}
                </p>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="font-semibold text-gray-900">Data Peminjam</h3>
                <div class="mt-4 space-y-4 text-sm">
                    <div>
                        <p class="text-gray-500">Nama</p>
                        <p class="mt-1 font-semibold text-gray-900">{{ $borrowing->borrower_name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">NIM</p>
                        <p class="mt-1 font-semibold text-gray-900">{{ $borrowing->borrower_nim ?? 'NIM belum tersedia' }}</p>
                    </div>
                </div>
            </div>

            <div class="theme-note-panel rounded-xl border border-[#d7e78a] bg-[#fbfde8] p-6 shadow-sm">
                <h3 class="theme-note-title font-semibold text-[#153b2d]">Catatan Peminjaman</h3>
                <ul class="theme-note-text mt-3 space-y-2 text-sm leading-6 text-[#315343]">
                    <li>Cek status dan catatan admin secara berkala.</li>
                    <li>Ambil barang hanya setelah pengajuan disetujui.</li>
                    <li>Kerusakan atau kehilangan menjadi tanggung jawab peminjam.</li>
                </ul>
            </div>
        </aside>
    </section>
</div>
@endsection
