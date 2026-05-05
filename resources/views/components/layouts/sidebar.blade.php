@props([
    'user' => null,
    'role' => null,
])

@php
    $userName = $user ?? auth()->user()?->name ?? 'User';
    $userRole = $role ?? auth()->user()?->role ?? 'Admin';

    $navItems = [
        [
            'label' => 'Dashboard',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>',
            'route' => 'dashboard',
            'active' => request()->routeIs('dashboard'),
        ],
        [
            'label' => 'Inventaris',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>',
            'route' => 'inventory.index',
            'active' => request()->routeIs('inventory.*'),
        ],
        [
            'label' => 'Barang Masuk',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m3 5H5m12 0a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
            'route' => 'incoming.index',
            'active' => request()->routeIs('incoming.*'),
        ],
        [
            'label' => 'Barang Keluar',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h5a2 2 0 012 2v1"/></svg>',
            'route' => 'outgoing.index',
            'active' => request()->routeIs('outgoing.*'),
        ],
        [
            'label' => 'Peminjaman',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>',
            'route' => 'borrowing.index',
            'active' => request()->routeIs('borrowing.*'),
        ],
        [
            'label' => 'Laporan',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
            'route' => 'reports.index',
            'active' => request()->routeIs('reports.*'),
        ],
    ];
@endphp

<aside
    id="sidebar"
    class="fixed top-0 left-0 z-40 h-screen w-64 bg-sidebar transition-transform duration-300 ease-in-out lg:translate-x-0 -translate-x-full"
>
    {{-- Logo / Brand --}}
    <div class="flex items-center justify-center h-16 border-b border-sidebar-hover/30">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-accent rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <span class="text-white font-bold text-lg">HMIF Inventory</span>
        </div>
    </div>

    {{-- User Profile Card --}}
    <div class="p-4 border-b border-sidebar-hover/30">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-accent/20 flex items-center justify-center">
                <span class="text-accent font-semibold text-sm">{{ strtoupper(substr($userName, 0, 2)) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-white text-sm font-medium truncate">{{ $userName }}</p>
                <p class="text-gray-400 text-xs">{{ $userRole }}</p>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto h-[calc(100vh-8rem)]">
        @foreach($navItems as $item)
            <a
                href="{{ route($item['route']) }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200
                    {{ $item['active']
                        ? 'bg-sidebar-active text-white'
                        : 'text-gray-300 hover:bg-sidebar-hover hover:text-white'
                    }}"
            >
                {!! $item['icon'] !!}
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    {{-- Logout --}}
    <div class="p-4 border-t border-sidebar-hover/30">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-sidebar-hover hover:text-white transition-colors duration-200"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h5a2 2 0 012 2v1"/>
                </svg>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>
