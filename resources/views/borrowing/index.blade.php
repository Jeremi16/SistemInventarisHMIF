@extends('layouts.master')

@section('title', 'Peminjaman Barang')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Daftar Peminjaman</h2>
            <p class="text-gray-500 mt-1">Kelola data peminjaman barang inventaris HMIF.</p>
        </div>
        <a href="{{ route('catalog.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-hmif-600 hover:bg-hmif-700 text-white font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-hmif-500 shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Pilih Barang
        </a>
    </div>

    {{-- Placeholder for Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 text-center text-gray-500 py-12">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <p>Belum ada data peminjaman yang diajukan.</p>
        </div>
    </div>
</div>
@endsection
