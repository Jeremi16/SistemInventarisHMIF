@props([
    'user' => null,
    'role' => null,
])

@php
    $sessionUser = session('user', []);
    $resolvedRole = $role ?? auth()->user()?->role ?? data_get($sessionUser, 'role');
    $isMember = request()->routeIs('member.*')
        || in_array(strtolower((string) $resolvedRole), ['anggota', 'member'], true);

    $roleLabels = [
        'admin' => 'Admin',
        'operator' => 'Operator',
        'member' => 'Anggota',
        'anggota' => 'Anggota',
    ];

    $userName = $user ?? auth()->user()?->name ?? data_get($sessionUser, 'name') ?? ($isMember ? 'Anggota HMIF' : 'User');
    $userRole = $roleLabels[strtolower((string) $resolvedRole)] ?? ($resolvedRole ?: ($isMember ? 'Anggota' : 'Admin'));

    $adminNavItems = [
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
            'label' => 'Mutasi Barang',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0-4-4m4 4-4 4M16 17H4m0 0 4 4m-4-4 4-4"/></svg>',
            'active' => request()->routeIs('incoming.*') || request()->routeIs('outgoing.*'),
            'children' => [
                [
                    'label' => 'Barang Masuk',
                    'route' => 'incoming.index',
                    'active' => request()->routeIs('incoming.*'),
                ],
                [
                    'label' => 'Barang Keluar',
                    'route' => 'outgoing.index',
                    'active' => request()->routeIs('outgoing.*'),
                ],
            ],
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

    $memberNavItems = [
        [
            'label' => 'Dashboard',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>',
            'route' => 'member.dashboard',
            'active' => request()->routeIs('member.dashboard'),
        ],
        [
            'label' => 'Katalog Barang',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>',
            'route' => 'catalog.index',
            'active' => request()->routeIs('catalog.*'),
        ],
        [
            'label' => 'Peminjaman Saya',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>',
            'route' => 'borrowing.index',
            'active' => request()->routeIs('borrowing.*'),
        ],
    ];

    if (strtolower((string) $resolvedRole) === 'admin') {
        array_splice($adminNavItems, 5, 0, [[
            'label' => 'Pengguna',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m6-6a4 4 0 11-8 0 4 4 0 018 0zm8 2a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
            'route' => 'users.index',
            'active' => request()->routeIs('users.*'),
        ]]);
    }

    $navItems = $isMember ? $memberNavItems : $adminNavItems;
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

    {{-- Navigation --}}
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto h-[calc(100vh-8rem)]">
        @foreach($navItems as $item)
            @if(isset($item['children']))
                <details class="sidebar-dropdown group" {{ $item['active'] ? 'open' : '' }}>
                    <summary
                        class="flex cursor-pointer list-none items-center justify-between rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-200
                            {{ $item['active']
                                ? 'bg-sidebar-active text-white'
                                : 'text-gray-300 hover:bg-sidebar-hover hover:text-white'
                            }}"
                    >
                        <span class="flex items-center gap-3">
                            {!! $item['icon'] !!}
                            <span>{{ $item['label'] }}</span>
                        </span>
                        <svg class="h-4 w-4 transition-transform duration-200 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
                        </svg>
                    </summary>
                    <div class="mt-1 space-y-1 border-l border-white/10 pl-4">
                        @foreach($item['children'] as $child)
                            <a
                                href="{{ route($child['route']) }}"
                                class="block rounded-lg px-3 py-2 text-sm font-medium transition-colors duration-200
                                    {{ $child['active']
                                        ? 'bg-white/15 text-white'
                                        : 'text-gray-300 hover:bg-sidebar-hover hover:text-white'
                                    }}"
                            >
                                {{ $child['label'] }}
                            </a>
                        @endforeach
                    </div>
                </details>
            @else
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
            @endif
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
