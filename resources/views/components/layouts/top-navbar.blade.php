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
        'approved' => 'Disetujui',
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
            ->map(fn ($notification) => $notification->id . ':' . $notification->status . ':' . ($notification->updated_at?->timestamp ?? 0))
            ->implode('|'));
    }
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
            {{-- Theme Switch --}}
            <div class="hmif-theme-switch" aria-label="Pilih tema tampilan">
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
                    class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors"
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

                <div class="hidden absolute right-0 mt-2 w-80 max-w-[calc(100vw-2rem)] overflow-hidden rounded-lg border border-gray-200 bg-white shadow-lg z-50">
                    <div class="border-b border-gray-100 px-4 py-3">
                        <p class="text-sm font-semibold text-gray-900">Notifikasi Status</p>
                        <p class="mt-0.5 text-xs text-gray-500">Perubahan status peminjaman dari admin.</p>
                    </div>
                    <div class="max-h-80 overflow-y-auto">
                        @forelse($statusNotifications as $notification)
                            <a href="{{ route('borrowing.show', $notification) }}" class="block border-b border-gray-100 px-4 py-3 last:border-b-0 hover:bg-gray-50">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-gray-900">{{ $notification->item_name }}</p>
                                        <p class="mt-1 text-xs text-gray-500">
                                            Status diubah menjadi {{ $statusLabels[$notification->status] ?? 'Diperbarui' }}.
                                        </p>
                                        @if($notification->admin_note)
                                            <p class="mt-1 line-clamp-2 text-xs text-gray-500">{{ $notification->admin_note }}</p>
                                        @endif
                                    </div>
                                    <span class="shrink-0 rounded-full bg-hmif-50 px-2 py-1 text-[11px] font-semibold text-hmif-700">
                                        {{ $statusLabels[$notification->status] ?? 'Update' }}
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
        });
    </script>
@endonce
