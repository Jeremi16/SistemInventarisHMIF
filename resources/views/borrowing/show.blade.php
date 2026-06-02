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
    @foreach(['handover_recorded', 'return_recorded', 'extension_requested', 'extension_approved', 'extension_rejected', 'note_added', 'note_deleted', 'pre_return_checked'] as $flash)
        @if(session($flash))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                {{ session($flash) }}
            </div>
        @endif
    @endforeach

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.14em] text-hmif-700">Detail Peminjaman</p>
            <h2 class="mt-2 text-2xl font-bold text-gray-900">{{ $borrowing->item_name }}</h2>
            <p class="mt-1 text-sm text-gray-500">Pengajuan #{{ $borrowing->id }} oleh {{ $borrowing->borrower_name }}</p>
        </div>
        <div class="flex flex-col gap-2 sm:flex-row">
            @if($isAdmin && in_array($borrowing->status, ['approved', 'overdue'], true))
                <a href="{{ route('borrowing.handover', $borrowing) }}" class="inline-flex items-center justify-center rounded-lg bg-hmif-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-hmif-700">
                    Catat Serah
                </a>
            @endif
            @if($isAdmin && in_array($borrowing->status, ['borrowed', 'overdue'], true))
                <a href="{{ route('borrowing.return', $borrowing) }}" class="inline-flex items-center justify-center rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-700">
                    Catat Kembali
                </a>
            @endif
            @if(! $isAdmin && in_array($borrowing->status, ['borrowed', 'overdue'], true))
                <a href="{{ route('borrowing.extension', $borrowing) }}" class="inline-flex items-center justify-center rounded-lg bg-hmif-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-hmif-700">
                    Perpanjang
                </a>
            @endif
            <a href="{{ $isAdmin ? route('borrowing.index') : route('member.dashboard') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                Kembali
            </a>
        </div>
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

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Penyerahan</p>
                    <p class="mt-2 text-sm text-gray-700">{{ $borrowing->handover_date ? $borrowing->handover_date->format('d M Y') : 'Belum dicatat' }}</p>
                    @if($borrowing->handover_condition)
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ ['good' => 'Baik', 'fair' => 'Layak Pakai', 'damaged' => 'Rusak'][$borrowing->handover_condition] ?? $borrowing->handover_condition }}</p>
                    @endif
                </div>
                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Pengembalian</p>
                    <p class="mt-2 text-sm text-gray-700">{{ $borrowing->return_date ? $borrowing->return_date->format('d M Y') : 'Belum dicatat' }}</p>
                    @if($borrowing->return_condition)
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ ['good' => 'Baik', 'fair' => 'Layak Pakai', 'damaged' => 'Rusak', 'lost' => 'Hilang'][$borrowing->return_condition] ?? $borrowing->return_condition }}</p>
                    @endif
                </div>
            </div>

            @if($fineAmount > 0 || $borrowing->fine_amount > 0)
                <div class="mt-6 rounded-lg border border-red-200 bg-red-50 p-4">
                    <p class="text-sm font-semibold text-red-800">Denda Keterlambatan</p>
                    <p class="mt-1 text-xl font-bold text-red-800">Rp {{ number_format(max((float) $borrowing->fine_amount, (float) $fineAmount), 0, ',', '.') }}</p>
                </div>
            @endif

            @if($borrowing->damage_description)
                <div class="mt-6">
                    <p class="text-sm font-semibold text-gray-900">Catatan Kerusakan / Kehilangan</p>
                    <p class="mt-2 rounded-lg bg-red-50 p-4 text-sm leading-6 text-red-800">{{ $borrowing->damage_description }}</p>
                </div>
            @endif
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

            @if($borrowing->extension_requested)
                <div class="rounded-xl border border-blue-200 bg-blue-50 p-6 shadow-sm">
                    <h3 class="font-semibold text-blue-900">Permintaan Perpanjangan</h3>
                    <p class="mt-2 text-sm text-blue-900">Tanggal baru: {{ $borrowing->extension_new_date?->format('d M Y') }}</p>
                    <p class="mt-2 text-sm text-blue-900">{{ $borrowing->extension_reason }}</p>
                    @if($isAdmin)
                        <div class="mt-4 flex flex-col gap-2">
                            <form method="POST" action="{{ route('borrowing.extension.approve', $borrowing) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">Setujui</button>
                            </form>
                            <form method="POST" action="{{ route('borrowing.extension.reject', $borrowing) }}" class="space-y-2">
                                @csrf
                                @method('PATCH')
                                <textarea name="admin_note" rows="2" class="w-full rounded-lg border border-blue-200 px-3 py-2 text-sm" placeholder="Alasan penolakan"></textarea>
                                <button type="submit" class="w-full rounded-lg border border-red-200 px-4 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50">Tolak</button>
                            </form>
                        </div>
                    @endif
                </div>
            @endif

            @if($isAdmin && in_array($borrowing->status, ['borrowed', 'overdue'], true))
                <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <h3 class="font-semibold text-gray-900">Pra-Pengembalian</h3>
                    <form method="POST" action="{{ route('borrowing.pre-return', $borrowing) }}" class="mt-4 space-y-3">
                        @csrf
                        <select name="pre_return_condition" required class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
                            @foreach(['good' => 'Baik', 'fair' => 'Layak Pakai', 'damaged' => 'Rusak'] as $value => $label)
                                <option value="{{ $value }}" @selected($borrowing->pre_return_condition === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <input type="date" name="pre_return_check_date" value="{{ old('pre_return_check_date', $borrowing->pre_return_check_date?->format('Y-m-d') ?? now()->toDateString()) }}" required class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
                        <button type="submit" class="w-full rounded-lg bg-hmif-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-hmif-700">Simpan Cek</button>
                    </form>
                </div>
            @endif
        </aside>
    </section>

    <section class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Catatan Berkala</h3>
                <p class="mt-1 text-sm text-gray-500">Catatan ini dapat dilihat peminjam untuk memantau kondisi atau instruksi admin.</p>
            </div>
            @if($isAdmin)
                <form method="POST" action="{{ route('borrowing.notes.store', $borrowing) }}" class="w-full space-y-3 sm:max-w-md">
                    @csrf
                    <textarea name="content" rows="3" required class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100" placeholder="Tambah catatan untuk peminjam"></textarea>
                    <button type="submit" class="rounded-lg bg-hmif-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-hmif-700">Tambah Catatan</button>
                </form>
            @endif
        </div>

        <div class="mt-6 divide-y divide-gray-100">
            @forelse($borrowing->notes as $note)
                <article class="flex gap-4 py-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-hmif-50 text-sm font-bold text-hmif-700">
                        {{ strtoupper(substr($note->user?->name ?? 'AD', 0, 2)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-semibold text-gray-900">{{ $note->user?->name ?? 'Admin' }}</p>
                            <p class="text-xs text-gray-400">{{ $note->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <p class="mt-2 text-sm leading-6 text-gray-700">{{ $note->content }}</p>
                    </div>
                    @if($isAdmin)
                        <form method="POST" action="{{ route('borrowing.notes.delete', $note) }}" onsubmit="return confirm('Hapus catatan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50">Hapus</button>
                        </form>
                    @endif
                </article>
            @empty
                <p class="py-6 text-sm text-gray-500">Belum ada catatan berkala.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
