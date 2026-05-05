@props([
    'user' => null,
    'role' => null,
])

@php
    $userName = $user ?? auth()->user()?->name ?? 'User';
    $userRole = $role ?? auth()->user()?->role ?? 'Admin';
@endphp

<header class="sticky top-0 z-20 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between h-16 px-4 md:px-6">
        {{-- Left: Mobile Menu Toggle + Page Title --}}
        <div class="flex items-center gap-4">
            <button
                onclick="toggleSidebar()"
                class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
                aria-label="Toggle sidebar"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <h1 class="text-lg font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
        </div>

        {{-- Right: Notifications + User Menu --}}
        <div class="flex items-center gap-3">
            {{-- Notification Bell --}}
            <button class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            {{-- User Dropdown --}}
            <div class="relative" x-data="{ open: false }">
                <button
                    onclick="this.nextElementSibling.classList.toggle('hidden')"
                    class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-100 transition-colors"
                >
                    <div class="hidden sm:block text-right">
                        <p class="text-sm font-medium text-gray-700">{{ $userName }}</p>
                        <p class="text-xs text-gray-500">{{ $userRole }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-hmif-100 flex items-center justify-center">
                        <span class="text-hmif-700 font-semibold text-sm">{{ strtoupper(substr($userName, 0, 2)) }}</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-500 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Dropdown Menu --}}
                <div class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        Profil Saya
                    </a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        Pengaturan
                    </a>
                    <div class="border-t border-gray-100 my-1"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
