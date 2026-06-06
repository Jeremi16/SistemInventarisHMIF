@extends('layouts.master')

@section('title', 'Profil Saya')

@section('content')
@php
    $role = strtolower((string) ($user?->role ?? data_get($sessionUser, 'role', 'member')));
    $roleLabels = [
        'admin' => 'Admin',
        'operator' => 'Operator',
        'member' => 'Anggota',
        'anggota' => 'Anggota',
    ];
    $name = $user?->name ?? data_get($sessionUser, 'name', 'User HMIF');
    $nim = $user?->nim ?? data_get($sessionUser, 'nim');
    $email = $user?->email;
@endphp

<div class="mx-auto max-w-4xl space-y-6">
    <section class="overflow-hidden rounded-xl bg-[#123829] text-white shadow-sm">
        <div class="p-4 sm:p-8">
            <p class="inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-lime-100 ring-1 ring-white/15">
                Profil Akun
            </p>
            <div class="mt-6 flex flex-col gap-5 sm:flex-row sm:items-center">
                <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full bg-white text-2xl font-bold text-[#1b8a1d]">
                    {{ strtoupper(substr($name, 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <h2 class="hmif-break-anywhere text-xl font-bold sm:text-2xl">{{ $name }}</h2>
                    <p class="mt-1 text-sm text-green-50/75">{{ $roleLabels[$role] ?? ucfirst($role) }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6">
        <h3 class="text-lg font-semibold text-gray-900">Informasi Akun</h3>
        <div class="mt-5 grid gap-4 sm:grid-cols-2">
            <div class="rounded-lg bg-gray-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Nama</p>
                <p class="mt-2 break-words text-sm font-semibold text-gray-900">{{ $name }}</p>
            </div>
            <div class="rounded-lg bg-gray-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Role</p>
                <p class="mt-2 text-sm font-semibold text-gray-900">{{ $roleLabels[$role] ?? ucfirst($role) }}</p>
            </div>
            <div class="rounded-lg bg-gray-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">NIM</p>
                <p class="mt-2 break-words text-sm font-semibold text-gray-900">{{ $nim ?: 'Belum tersedia' }}</p>
            </div>
            <div class="rounded-lg bg-gray-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Email</p>
                <p class="mt-2 break-words text-sm font-semibold text-gray-900">{{ $email ?: 'Belum tersedia' }}</p>
            </div>
        </div>
    </section>
</div>
@endsection
