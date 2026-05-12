<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'HMIF Inventory') }}</title>
    <script>
        (function () {
            try {
                const savedTheme = window.localStorage.getItem('hmif-theme');
                const theme = savedTheme === 'dark' ? 'dark' : 'light';

                document.documentElement.dataset.theme = theme;
                document.documentElement.classList.toggle('theme-dark', theme === 'dark');
            } catch (error) {
                document.documentElement.dataset.theme = 'light';
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased transition-colors duration-200">
    <div class="min-h-screen flex">
        {{-- Sidebar --}}
        <x-layouts.sidebar />

        {{-- Main Content Area --}}
        <div class="flex-1 flex flex-col lg:ml-64">
            {{-- Top Navbar --}}
            <x-layouts.top-navbar />

            {{-- Page Content --}}
            <main class="flex-1 p-4 md:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Mobile Sidebar Overlay --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden" onclick="toggleSidebar()"></div>

    @stack('scripts')
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        function getHmifTheme() {
            try {
                return window.localStorage.getItem('hmif-theme') === 'dark' ? 'dark' : 'light';
            } catch (error) {
                return 'light';
            }
        }

        function applyHmifTheme(theme) {
            const selectedTheme = theme === 'dark' ? 'dark' : 'light';

            document.documentElement.dataset.theme = selectedTheme;
            document.documentElement.classList.toggle('theme-dark', selectedTheme === 'dark');

            document.querySelectorAll('[data-theme-option]').forEach(function (button) {
                const isActive = button.dataset.themeOption === selectedTheme;
                button.classList.toggle('is-active', isActive);
                button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            });
        }

        function setHmifTheme(theme) {
            const selectedTheme = theme === 'dark' ? 'dark' : 'light';

            try {
                window.localStorage.setItem('hmif-theme', selectedTheme);
            } catch (error) {
                // Theme still applies for the current page if storage is unavailable.
            }

            applyHmifTheme(selectedTheme);
        }

        document.addEventListener('DOMContentLoaded', function () {
            applyHmifTheme(getHmifTheme());
        });
    </script>
</body>
</html>
