@extends('layouts.master')

@section('title', 'Edit Barang')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edit Barang</h2>
            <p class="mt-1 text-sm text-gray-500">Perbarui data inventaris {{ $item->name }}.</p>
        </div>
        <a href="{{ route('inventory.show', $item) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
            Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold">Perubahan belum dapat disimpan.</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('inventory.update', $item) }}" enctype="multipart/form-data" class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')

        <div class="grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="name" class="mb-2 block text-sm font-semibold text-gray-700">Nama Barang</label>
                <input id="name" name="name" type="text" value="{{ old('name', $item->name) }}" required class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            </div>

            <div>
                <label for="category" class="mb-2 block text-sm font-semibold text-gray-700">Kategori</label>
                <input id="category" name="category" type="text" value="{{ old('category', $item->category) }}" required class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            </div>

            <div>
                <label for="quantity" class="mb-2 block text-sm font-semibold text-gray-700">Jumlah Stok</label>
                <input id="quantity" name="quantity" type="number" min="0" value="{{ old('quantity', $item->quantity) }}" required class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            </div>

            <div>
                <label for="status" class="mb-2 block text-sm font-semibold text-gray-700">Status</label>
                <select id="status" name="status" required class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $item->status) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="condition" class="mb-2 block text-sm font-semibold text-gray-700">Kondisi</label>
                <select id="condition" name="condition" required class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
                    @foreach($conditions as $value => $label)
                        <option value="{{ $value }}" @selected(old('condition', $item->condition) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <label for="location" class="mb-2 block text-sm font-semibold text-gray-700">Lokasi Penyimpanan</label>
                <input id="location" name="location" type="text" value="{{ old('location', $item->location) }}" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            </div>

            <div class="sm:col-span-2">
                <label for="description" class="mb-2 block text-sm font-semibold text-gray-700">Deskripsi</label>
                <textarea id="description" name="description" rows="4" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">{{ old('description', $item->description) }}</textarea>
            </div>

            <div class="sm:col-span-2">
                <label for="photo" class="mb-2 block text-sm font-semibold text-gray-700">Foto Barang</label>
                @if($item->photo)
                    <img src="{{ asset('storage/' . $item->photo) }}" alt="Foto {{ $item->name }}" class="mb-3 h-32 w-32 rounded-lg border border-gray-200 object-cover">
                @endif
                <input id="photo" name="photo" type="file" accept="image/*" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm file:mr-4 file:rounded-md file:border-0 file:bg-hmif-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-hmif-700 focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
                <p class="mt-1 text-xs text-gray-400">Kosongkan jika tidak ingin mengganti foto.</p>
            </div>
        </div>

        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-between">
            <button type="button" onclick="document.getElementById('delete-item-form').submit()" class="inline-flex items-center justify-center rounded-lg border border-red-200 px-5 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-50">
                Hapus Barang
            </button>
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('inventory.show', $item) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-hmif-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-hmif-700">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>

    <form id="delete-item-form" method="POST" action="{{ route('inventory.destroy', $item) }}" onsubmit="return confirm('Hapus barang ini dari inventaris aktif?')" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection
