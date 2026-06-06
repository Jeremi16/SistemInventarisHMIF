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

    $statusLabels = [
        'approved' => 'Siap Diambil',
        'rejected' => 'Ditolak',
        'borrowed' => 'Dipinjam',
        'returned' => 'Dikembalikan',
        'overdue' => 'Terlambat',
    ];
    $memberNim = auth()->user()?->nim ?? data_get($sessionUser, 'nim');
    $statusNotifications = collect();

    if ($isMember && filled($memberNim) && \Illuminate\Support\Facades\Schema::hasTable('borrowings')) {
        $statusNotifications = \App\Models\Borrowing::query()
            ->where('borrower_nim', $memberNim)
            ->whereIn('status', array_keys($statusLabels))
            ->latest('updated_at')
            ->take(5)
            ->get();
    }

    $notificationCount = $statusNotifications->count();
    $notificationReadKey = null;
    $notificationStorageKey = filled($memberNim)
        ? 'hmif-notifications-read-' . $memberNim
        : 'hmif-notifications-read';

    if ($notificationCount > 0) {
        $notificationReadKey = sha1($memberNim . '|' . $statusNotifications
            ->map(fn ($notification) => $notification->id . ':' . $notification->status . ':' . ($notification->updated_at?->timestamp ?? 0) . ':' . ($notification->extension_rejected_at?->timestamp ?? 0))
            ->implode('|'));
    }
@endphp

<header class="sticky top-0 z-20 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex h-16 items-center justify-between gap-2 px-3 sm:px-4 md:px-6">
        {{-- Left: Mobile Menu Toggle + Page Title --}}
        <div class="min-w-0 flex flex-1 items-center gap-2 sm:gap-4">
            <button
                onclick="toggleSidebar()"
                class="shrink-0 rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 lg:hidden"
                aria-label="Toggle sidebar"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <h1 class="min-w-0 truncate text-base font-semibold text-gray-800 sm:text-lg">@yield('title', 'Dashboard')</h1>
        </div>

        {{-- Right: Notifications + User Menu --}}
        <div class="flex shrink-0 items-center gap-1 sm:gap-2 md:gap-3">
            {{-- Theme Switch --}}
            <div class="hmif-theme-switch hidden sm:grid" aria-label="Pilih tema tampilan">
                <button
                    type="button"
                    data-theme-option="light"
                    onclick="setHmifTheme('light')"
                    class="hmif-theme-option"
                    aria-label="Gunakan light mode"
                    aria-pressed="true"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364-.707-.707M6.343 6.343l-.707-.707m12.728 0-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <!-- <span class="hidden md:inline">Light</span> -->
                </button>
                <button
                    type="button"
                    data-theme-option="dark"
                    onclick="setHmifTheme('dark')"
                    class="hmif-theme-option"
                    aria-label="Gunakan dark mode"
                    aria-pressed="false"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                    </svg>
                    <!-- <span class="hidden md:inline">Dark</span> -->
                </button>
            </div>

            {{-- Notification Bell --}}
            <div class="relative">
                <button
                    type="button"
                    id="notification-button"
                    data-notification-key="{{ $notificationReadKey }}"
                    data-notification-storage-key="{{ $notificationStorageKey }}"
                    onclick="toggleNotificationDropdown(this)"
                    class="relative rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700"
                    aria-label="Buka notifikasi"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($notificationCount > 0)
                        <span id="notification-badge" class="absolute -top-0.5 -right-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">
                            {{ $notificationCount }}
                        </span>
                    @endif
                </button>

                <div class="hidden absolute right-0 mt-2 w-80 max-w-[calc(100vw-1rem)] overflow-hidden rounded-lg border border-gray-200 bg-white shadow-lg z-50">
                    <div class="border-b border-gray-100 px-4 py-3">
                        <p class="text-sm font-semibold text-gray-900">Notifikasi Peminjaman</p>
                        <p class="mt-0.5 text-xs text-gray-500">Status peminjaman dan keputusan perpanjangan dari admin.</p>
                    </div>
                    <div class="max-h-80 overflow-y-auto">
                        @forelse($statusNotifications as $notification)
                            @php
                                $isExtensionRejected = filled($notification->extension_rejection_reason) && filled($notification->extension_rejected_at);
                            @endphp
                            <a href="{{ route('borrowing.show', $notification) }}" class="block border-b border-gray-100 px-4 py-3 last:border-b-0 hover:bg-gray-50">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-gray-900">{{ $notification->item_name }}</p>
                                        <p class="mt-1 text-xs text-gray-500">
                                            @if($isExtensionRejected)
                                                Perpanjangan peminjaman ditolak admin.
                                            @else
                                                Status diubah menjadi {{ $statusLabels[$notification->status] ?? 'Diperbarui' }}.
                                            @endif
                                        </p>
                                        @if($isExtensionRejected)
                                            <p class="mt-1 line-clamp-2 text-xs text-red-600">{{ $notification->extension_rejection_reason }}</p>
                                        @elseif($notification->admin_note)
                                            <p class="mt-1 line-clamp-2 text-xs text-gray-500">{{ $notification->admin_note }}</p>
                                        @endif
                                    </div>
                                    <span class="shrink-0 rounded-full {{ $isExtensionRejected ? 'bg-red-50 text-red-700' : 'bg-hmif-50 text-hmif-700' }} px-2 py-1 text-[11px] font-semibold">
                                        {{ $isExtensionRejected ? 'Perpanjangan' : ($statusLabels[$notification->status] ?? 'Update') }}
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="px-4 py-6 text-center text-sm text-gray-500">
                                Belum ada perubahan status peminjaman.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- User Dropdown --}}
            <div class="relative" data-user-menu-root>
                <button
                    type="button"
                    onclick="toggleUserDropdown(this)"
                    class="flex items-center gap-2 rounded-xl border border-transparent p-1.5 transition-colors hover:border-gray-200 hover:bg-gray-50 sm:gap-3 sm:p-2"
                    aria-haspopup="menu"
                    aria-expanded="false"
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
                <div class="hidden absolute right-0 mt-2 w-64 max-w-[calc(100vw-1rem)] overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl z-50" role="menu">
                    <div class="border-b border-gray-100 px-4 py-3">
                        <p class="truncate text-sm font-semibold text-gray-900">{{ $userName }}</p>
                        <p class="mt-0.5 text-xs text-gray-500">{{ $userRole }}</p>
                    </div>
                    <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-hmif-50 hover:text-hmif-800" role="menuitem">
                        <svg class="h-4 w-4 text-hmif-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 7.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a8.25 8.25 0 1 1 16.5 0"/>
                        </svg>
                        <span>Profil Saya</span>
                    </a>
                    <div class="border-t border-gray-100"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm font-medium text-red-600 transition hover:bg-red-50">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3-3H9.75m0 0 3-3m-3 3 3 3"/>
                            </svg>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

@once
    <script>
        function getStoredNotificationKey(storageKey) {
            try {
                return window.localStorage.getItem(storageKey);
            } catch (error) {
                return null;
            }
        }

        function setStoredNotificationKey(storageKey, notificationKey) {
            try {
                window.localStorage.setItem(storageKey, notificationKey);
            } catch (error) {
                return;
            }
        }

        function hideNotificationBadge() {
            const badge = document.getElementById('notification-badge');

            if (badge) {
                badge.classList.add('hidden');
            }
        }

        function toggleNotificationDropdown(button) {
            button.nextElementSibling.classList.toggle('hidden');

            const notificationKey = button.dataset.notificationKey;
            const storageKey = button.dataset.notificationStorageKey;

            if (notificationKey && storageKey) {
                setStoredNotificationKey(storageKey, notificationKey);
                hideNotificationBadge();
            }
        }

        function toggleUserDropdown(button) {
            const menu = button.nextElementSibling;
            const willOpen = menu.classList.contains('hidden');

            menu.classList.toggle('hidden');
            button.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const button = document.getElementById('notification-button');

            if (!button) {
                return;
            }

            const notificationKey = button.dataset.notificationKey;
            const storageKey = button.dataset.notificationStorageKey;

            if (notificationKey && storageKey && getStoredNotificationKey(storageKey) === notificationKey) {
                hideNotificationBadge();
            }

            document.addEventListener('click', function (event) {
                document.querySelectorAll('[data-user-menu-root]').forEach(function (root) {
                    if (root.contains(event.target)) {
                        return;
                    }

                    const menuButton = root.querySelector('button[aria-haspopup="menu"]');
                    const menu = menuButton?.nextElementSibling;

                    if (menu && !menu.classList.contains('hidden')) {
                        menu.classList.add('hidden');
                        menuButton.setAttribute('aria-expanded', 'false');
                    }
                });
            });
        });
    </script>
@endonce
