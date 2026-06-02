@extends('layouts.master')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="space-y-6">
    @foreach(['user_created', 'user_updated', 'user_deleted'] as $flash)
        @if(session($flash))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                {{ session($flash) }}
            </div>
        @endif
    @endforeach

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Manajemen Pengguna</h2>
            <p class="mt-1 text-sm text-gray-500">Daftarkan anggota, operator, dan admin sesuai hak akses.</p>
        </div>
        <a href="{{ route('users.create') }}" class="inline-flex items-center justify-center rounded-lg bg-hmif-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-hmif-700">
            Pengguna Baru
        </a>
    </div>

    <form method="GET" action="{{ route('users.index') }}" class="grid gap-3 rounded-xl border border-gray-100 bg-white p-4 shadow-sm md:grid-cols-[1fr_14rem_auto_auto]">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau NIM" class="rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
        <select name="role" class="rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm focus:border-hmif-500 focus:outline-none focus:ring-2 focus:ring-hmif-100">
            <option value="all">Semua Role</option>
            @foreach($roles as $role)
                <option value="{{ $role }}" @selected(request('role') === $role)>{{ ucfirst($role) }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-lg bg-hmif-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-hmif-700">Filter</button>
        <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Reset</a>
    </form>

    <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="border-b border-gray-100 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-5 py-4">Nama</th>
                        <th class="px-5 py-4">Email</th>
                        <th class="px-5 py-4">NIM</th>
                        <th class="px-5 py-4">Role</th>
                        <th class="px-5 py-4">Kontak</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4 font-semibold text-gray-900">{{ $user->name }}</td>
                            <td class="px-5 py-4">{{ $user->email }}</td>
                            <td class="px-5 py-4">{{ $user->nim ?: '-' }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full bg-hmif-50 px-2.5 py-1 text-xs font-semibold text-hmif-700">{{ ucfirst($user->role) }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <p>{{ $user->phone ?: '-' }}</p>
                                <p class="text-xs text-gray-400">{{ $user->batch ? 'Angkatan ' . $user->batch : '' }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->is_active ?? true ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $user->is_active ?? true ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="inline-flex gap-2">
                                    <a href="{{ route('users.edit', $user) }}" class="rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">Edit</a>
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Nonaktifkan pengguna ini? Histori peminjaman tetap tersimpan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50">Nonaktifkan</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-gray-500">Tidak ada pengguna sesuai filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-100 px-5 py-4">
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
