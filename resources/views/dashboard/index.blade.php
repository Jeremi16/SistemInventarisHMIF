@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
@php
    $statusConfig = [
        'pending' => ['label' => 'Menunggu', 'class' => 'bg-yellow-100 text-yellow-800'],
        'approved' => ['label' => 'Siap Diambil', 'class' => 'bg-hmif-100 text-hmif-800'],
        'rejected' => ['label' => 'Ditolak', 'class' => 'bg-red-100 text-red-800'],
        'borrowed' => ['label' => 'Dipinjam', 'class' => 'bg-green-100 text-green-800'],
        'returned' => ['label' => 'Dikembalikan', 'class' => 'bg-green-100 text-green-800'],
        'overdue' => ['label' => 'Terlambat', 'class' => 'bg-red-100 text-red-800'],
    ];
    $recentBorrowings = $recentBorrowings ?? collect();
    $dashboardStats = $dashboardStats ?? [
        'total_items' => 0,
        'borrowed_items' => 0,
        'maintenance_items' => 0,
        'overdue_borrowings' => 0,
    ];
@endphp

<div class="space-y-6">
    @if(session('borrowing_status_updated'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
            {{ session('borrowing_status_updated') }}
        </div>
    @endif

    {{-- Welcome Banner --}}
    <div class="rounded-xl bg-gradient-to-r from-hmif-800 to-hmif-600 p-4 text-white shadow-sm sm:p-6">
        <h2 class="hmif-break-anywhere text-xl font-bold sm:text-2xl">Selamat Datang, {{ auth()->user()?->name ?? 'Admin' }}!</h2>
        <p class="text-hmif-200 mt-1">Sistem Manajemen Inventaris HMIF ITERA</p>
    </div>

    {{-- Stats Grid (F-20) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Items --}}
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Barang</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $dashboardStats['total_items'] }}</p>
                </div>
                <div class="w-12 h-12 bg-hmif-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-hmif-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Currently Borrowed --}}
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Sedang Dipinjam</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $dashboardStats['borrowed_items'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Items Needing Maintenance --}}
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Butuh Perawatan</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $dashboardStats['maintenance_items'] }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Overdue Loans --}}
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Terlambat</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $dashboardStats['overdue_borrowings'] }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Loan Activities --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col gap-3 border-b border-gray-100 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-6">
            <h3 class="text-base font-semibold text-gray-800 sm:text-lg">Aktivitas Peminjaman Terbaru</h3>
            <a href="{{ route('borrowing.index') }}" class="text-sm text-hmif-600 hover:text-hmif-700 font-medium">Lihat Semua</a>
        </div>
        <div class="hmif-table-scroll">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Peminjam</th>
                        <th class="px-6 py-4">Barang</th>
                        <th class="px-6 py-4">Tanggal Pinjam</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentBorrowings as $borrowing)
                        @php
                            $status = $statusConfig[$borrowing->status] ?? $statusConfig['pending'];
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-800">{{ $borrowing->borrower_name }}</p>
                                <p class="text-xs text-gray-400">{{ $borrowing->borrower_nim ?? 'NIM belum tersedia' }}</p>
                            </td>
                            <td class="px-6 py-4">{{ $borrowing->item_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $borrowing->startDateTime()?->format('d M Y H:i:s') }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status['class'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">
                                Belum ada pengajuan peminjaman dari member.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
