<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Anggota - HMIF Inventory</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f7faf7] font-sans text-gray-900 antialiased">
    <main class="min-h-screen grid lg:grid-cols-[1.05fr_0.95fr]">
        <section class="hidden lg:flex relative overflow-hidden bg-[#153b2d] text-white">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_18%_18%,rgba(205,220,57,0.22),transparent_28%),radial-gradient(circle_at_84%_70%,rgba(76,175,80,0.26),transparent_30%)]"></div>
            <div class="relative z-10 flex min-h-screen w-full flex-col justify-between p-12">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-hmif.png') }}" alt="Logo HMIF" class="h-12 w-12 rounded-full bg-white object-cover p-1">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-lime-200">HMIF ITERA</p>
                        <h1 class="text-2xl font-bold">Inventory Member</h1>
                    </div>
                </div>

                <div class="max-w-xl">
                    <p class="mb-5 inline-flex rounded-full bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-lime-100 ring-1 ring-white/15">Portal Anggota</p>
                    <h2 class="text-5xl font-extrabold leading-tight tracking-tight">Masuk untuk mengajukan dan memantau peminjaman barang HMIF.</h2>
                    <p class="mt-6 max-w-lg text-base leading-7 text-green-50/80">Akses member mengikuti SKPL: katalog barang, pengajuan peminjaman, status persetujuan, catatan admin, dan histori pribadi.</p>
                </div>
            </div>
        </section>

        <section class="flex min-h-screen items-center justify-center px-5 py-10 sm:px-8">
            <div class="w-full max-w-md">
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    <img src="{{ asset('images/logo-hmif.png') }}" alt="Logo HMIF" class="h-11 w-11 rounded-full object-cover">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#1b8a1d]">HMIF ITERA</p>
                        <h1 class="text-xl font-bold">Inventory Member</h1>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
                    <div class="mb-7">
                        <p class="text-sm font-semibold uppercase tracking-[0.16em] text-[#1b8a1d]">Login Anggota</p>
                        <h2 class="mt-2 text-2xl font-bold text-gray-950">Masuk ke akun member</h2>
                        <p class="mt-2 text-sm leading-6 text-gray-500">Gunakan email HMIF atau NIM yang terdaftar oleh admin.</p>
                    </div>

                    @if($errors->any())
                        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('login.attempt') }}" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label for="identifier" class="mb-2 block text-sm font-semibold text-gray-700">Email atau NIM</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0ZM12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7Z"/>
                                    </svg>
                                </div>
                                <input
                                    id="identifier"
                                    name="identifier"
                                    type="text"
                                    value="{{ old('identifier') }}"
                                    autocomplete="username"
                                    required
                                    class="w-full rounded-lg border border-gray-200 bg-white py-3 pl-11 pr-4 text-sm outline-none transition focus:border-[#1b8a1d] focus:ring-4 focus:ring-green-100"
                                    placeholder="member@hmif.itera.ac.id"
                                >
                            </div>
                        </div>

                        <div>
                            <div class="mb-2 flex items-center justify-between gap-3">
                                <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>
                                <a href="#" class="text-sm font-medium text-[#1b8a1d] hover:text-[#176d19]">Lupa password?</a>
                            </div>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 10.5V7.75a4.5 4.5 0 0 0-9 0v2.75M6.75 21h10.5A2.25 2.25 0 0 0 19.5 18.75v-6A2.25 2.25 0 0 0 17.25 10.5H6.75A2.25 2.25 0 0 0 4.5 12.75v6A2.25 2.25 0 0 0 6.75 21Z"/>
                                    </svg>
                                </div>
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    autocomplete="current-password"
                                    required
                                    class="w-full rounded-lg border border-gray-200 bg-white py-3 pl-11 pr-4 text-sm outline-none transition focus:border-[#1b8a1d] focus:ring-4 focus:ring-green-100"
                                    placeholder="Masukkan password"
                                >
                            </div>
                        </div>

                        <label class="flex items-center gap-3 text-sm text-gray-600">
                            <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-gray-300 text-[#1b8a1d] focus:ring-[#1b8a1d]">
                            <span>Ingat saya di perangkat ini</span>
                        </label>

                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#1b8a1d] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#176d19] focus:outline-none focus:ring-4 focus:ring-green-100">
                            <span>Masuk</span>
                            <!-- <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"> -->
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0-4 4m4-4H3"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
