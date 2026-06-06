@extends('layouts.master')

@section('title', 'Pengguna Baru')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <h2 class="text-xl font-bold text-gray-900 sm:text-2xl">Pengguna Baru</h2>
            <p class="mt-1 text-sm text-gray-500">Daftarkan pengguna dan tentukan role awal.</p>
        </div>
        <a href="{{ route('users.index') }}" class="inline-flex w-full items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 sm:w-auto">Kembali</a>
    </div>

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold">Pengguna belum dapat disimpan.</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('users.store') }}" class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6">
        @csrf
        <div class="grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="name" class="mb-2 block text-sm font-semibold text-gray-700">Nama</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            </div>

            <div>
                <label for="email" class="mb-2 block text-sm font-semibold text-gray-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            </div>

            <div>
                <label for="nim" class="mb-2 block text-sm font-semibold text-gray-700">NIM</label>
                <input id="nim" name="nim" type="text" value="{{ old('nim') }}" required class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            </div>

            <div>
                <label for="password" class="mb-2 block text-sm font-semibold text-gray-700">Password</label>
                <input id="password" name="password" type="password" required class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            </div>

            <div>
                <label for="role" class="mb-2 block text-sm font-semibold text-gray-700">Role</label>
                <select id="role" name="role" required class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
                    @foreach($roles as $role)
                        <option value="{{ $role }}" @selected(old('role', 'member') === $role)>{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="phone" class="mb-2 block text-sm font-semibold text-gray-700">Nomor HP</label>
                <input id="phone" name="phone" type="text" value="{{ old('phone') }}" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            </div>

            <div>
                <label for="batch" class="mb-2 block text-sm font-semibold text-gray-700">Angkatan</label>
                <input id="batch" name="batch" type="text" value="{{ old('batch') }}" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            </div>

            <label class="sm:col-span-2 flex items-center gap-3 rounded-lg border border-gray-100 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-700">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-hmif-600 focus:ring-hmif-500">
                Akun aktif
            </label>
        </div>

        <div class="hmif-mobile-actions mt-6 sm:justify-end">
            <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Batal</a>
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-hmif-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-hmif-700">Simpan</button>
        </div>
    </form>
</div>
@endsection
