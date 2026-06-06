@extends('layouts.master')

@section('title', 'Peminjaman Barang')

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
@endphp

<div class="space-y-6">
    @if(session('borrowing_status_updated'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
            {{ session('borrowing_status_updated') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="min-w-0">
            <h2 class="text-xl font-bold text-gray-800 sm:text-2xl">{{ $isAdmin ? 'Daftar Peminjaman' : 'Peminjaman Saya' }}</h2>
            <p class="text-gray-500 mt-1">{{ $isAdmin ? 'Ubah status pengajuan dan tambahkan catatan untuk member.' : 'Pantau status pengajuan dan catatan dari admin.' }}</p>
        </div>
        <a href="{{ route('catalog.index') }}" class="inline-flex w-full items-center justify-center px-4 py-2 bg-hmif-600 hover:bg-hmif-700 text-white font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-hmif-500 shadow-sm sm:w-auto">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Pilih Barang
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="hmif-table-scroll">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Peminjam</th>
                        <th class="px-6 py-4">Barang</th>
                        <th class="px-6 py-4">Durasi</th>
                        <th class="px-6 py-4">Status</th>
                        @if($isAdmin)
                            <th class="px-6 py-4">Ubah Status</th>
                        @endif
                        <th class="px-6 py-4">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($borrowings as $borrowing)
                        @php
                            $status = $statusConfig[$borrowing->status] ?? $statusConfig['pending'];
                        @endphp
                        <tr class="align-top hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-900">{{ $borrowing->borrower_name }}</p>
                                <p class="text-xs text-gray-400">{{ $borrowing->borrower_nim ?? 'NIM belum tersedia' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-800">{{ $borrowing->item_name }}</p>
                                @if(\Illuminate\Support\Str::length($borrowing->purpose) > 120)
                                    <details class="mt-1 max-w-sm text-xs text-gray-500">
                                        <summary class="font-semibold text-hmif-700 hover:text-hmif-800">Lihat Keperluan</summary>
                                        <p class="mt-2 whitespace-pre-line break-words leading-5">{{ $borrowing->purpose }}</p>
                                    </details>
                                @else
                                    <p class="mt-1 max-w-sm whitespace-pre-line break-words text-xs leading-5 text-gray-500">{{ $borrowing->purpose }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $borrowing->startDateTime()?->format('d M Y H:i:s') }}<br>
                                <span class="text-xs text-gray-400">s.d. {{ $borrowing->endDateTime()?->format('d M Y H:i:s') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status['class'] }}">
                                    {{ $status['label'] }}
                                </span>
                                @if($borrowing->admin_note)
                                    <p class="mt-2 max-w-xs text-xs leading-5 text-gray-500">{{ $borrowing->admin_note }}</p>
                                @endif
                            </td>
                            @if($isAdmin)
                                <td class="px-6 py-4">
                                    <form method="POST" action="{{ route('borrowing.status.update', $borrowing) }}" class="min-w-64 space-y-3 sm:min-w-72">
                                        @csrf
                                        @method('PATCH')
                                        <div class="flex flex-col gap-2 sm:flex-row">
                                            <select name="status" class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100 sm:w-40">
                                                @foreach($statuses as $value => $label)
                                                    <option value="{{ $value }}" @selected($borrowing->status === $value)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="rounded-lg bg-hmif-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-hmif-700">
                                                Simpan
                                            </button>
                                        </div>
                                        <textarea
                                            name="admin_note"
                                            rows="2"
                                            class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100"
                                            placeholder="Catatan untuk peminjam"
                                        >{{ old('admin_note', $borrowing->admin_note) }}</textarea>
                                    </form>
                                </td>
                            @endif
                            <td class="px-6 py-4">
                                <a href="{{ route('borrowing.show', $borrowing) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? 6 : 5 }}" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p>Belum ada data peminjaman yang diajukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($borrowings, 'links'))
            <div class="border-t border-gray-100 px-6 py-4">
                {{ $borrowings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
