<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Inventaris HMIF</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|plus-jakarta-sans:500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1,h2,h3,h4,h5,h6,.font-heading { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="antialiased bg-white text-gray-800">

<!-- Navbar -->
<nav class="w-full bg-white border-b border-gray-100 py-4 px-6 md:px-12 flex justify-between items-center z-50 sticky top-0">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center border-[3px] border-[#0A192F]">
            <span class="font-bold text-[#0A192F] text-xs">H</span>
        </div>
        <div class="font-bold text-sm leading-tight text-gray-900">
            HMIF Inventory<br><span class="text-[10px] text-gray-500 font-normal uppercase tracking-wider">Sistem HMIF</span>
        </div>
    </div>
    <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
        <a href="#fitur" class="hover:text-gray-900 transition-colors">Features</a>
        <a href="#tutorial" class="hover:text-gray-900 transition-colors">Tutorial</a>
        <a href="#faq" class="hover:text-gray-900 transition-colors">FAQ</a>
        <a href="{{ url('/dashboard') }}" class="hover:text-gray-900 transition-colors">Dashboard</a>
    </div>
    <div>
        @auth
            <a href="{{ url('/dashboard') }}" class="bg-[#4CAF50] hover:bg-[#43a047] text-white px-6 py-2 rounded-full text-sm font-semibold transition-colors shadow-sm">Dashboard</a>
        @else
            <a href="{{ url('/login') }}" class="bg-[#4CAF50] hover:bg-[#43a047] text-white px-6 py-2 rounded-full text-sm font-semibold transition-colors shadow-sm">Login</a>
        @endauth
    </div>
</nav>

<!-- Hero -->
<div class="pt-20 pb-16 px-6 max-w-5xl mx-auto text-center">
    <span class="inline-block bg-[#4CAF50] text-white text-[11px] font-bold px-3 py-1 rounded-full mb-6 tracking-wide">HMIF PORTAL</span>
    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold font-heading text-[#111827] leading-tight mb-6 tracking-tight">
        Sistem Inventaris dan<br>Peminjaman Barang HMIF
    </h1>
    <p class="text-gray-500 max-w-2xl mx-auto mb-10 text-lg leading-relaxed">
        Kelola aset himpunan dengan presisi, cepat, transparan, dan terorganisir untuk seluruh anggota HMIF.
    </p>
    <a href="#tutorial" class="inline-block bg-[#4CAF50] hover:bg-[#43a047] text-white font-semibold px-8 py-3.5 rounded-full transition-transform hover:-translate-y-0.5 shadow-lg shadow-green-500/25 mb-16">
        Lihat Tutorial
    </a>
</div>

<!-- Hero Mockup -->
<div class="max-w-5xl mx-auto px-6 mb-32">
    <div class="bg-gray-50 rounded-3xl p-6 md:p-8 shadow-xl border border-gray-100/50 flex">
        <!-- Sidebar -->
        <div class="w-16 bg-white/60 backdrop-blur-sm rounded-2xl flex flex-col items-center py-6 gap-6 border border-gray-200/50 mr-8 hidden md:flex">
            <div class="w-6 h-6 rounded-md bg-blue-500"></div>
            <div class="w-5 h-5 rounded-md border-2 border-gray-300"></div>
            <div class="w-5 h-5 rounded-md border-2 border-gray-300"></div>
            <div class="w-5 h-5 rounded-md border-2 border-gray-300"></div>
        </div>
        <!-- Main -->
        <div class="flex-1 flex flex-col gap-6">
            <div class="flex justify-end"><div class="w-8 h-8 rounded-full bg-blue-100"></div></div>
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 h-28 flex flex-col justify-center gap-2">
                    <div class="w-1/3 h-2 bg-gray-200 rounded-full"></div>
                    <div class="w-2/3 h-3.5 bg-blue-300 rounded-full"></div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 h-28 flex flex-col justify-center gap-2">
                    <div class="w-1/3 h-2 bg-gray-200 rounded-full"></div>
                    <div class="w-2/3 h-3.5 bg-amber-300 rounded-full"></div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 h-28 flex flex-col justify-center gap-2">
                    <div class="w-1/3 h-2 bg-gray-200 rounded-full"></div>
                    <div class="w-2/3 h-3.5 bg-gray-300 rounded-full"></div>
                </div>
            </div>
            <div class="flex flex-col gap-3">
                <div class="w-full h-10 bg-white/60 rounded-xl border border-gray-100"></div>
                <div class="w-full h-10 bg-white/60 rounded-xl border border-gray-100"></div>
                <div class="w-full h-10 bg-white/60 rounded-xl border border-gray-100"></div>
            </div>
        </div>
    </div>
</div>

<!-- Fitur Utama -->
<div id="fitur" class="bg-[#F8F9FA] py-24">
    <div class="max-w-7xl mx-auto px-6">
        <div class="mb-14">
            <h2 class="text-3xl font-bold font-heading text-gray-900 mb-3">Fitur Utama</h2>
            <p class="text-gray-500 max-w-xl text-sm leading-relaxed">Didesain untuk menyederhanakan birokrasi dan menjaga keamanan aset himpunan.</p>
        </div>
        <div class="grid md:grid-cols-4 gap-6">
            @php
            $features = [
                ['Management', 'Pengarsipan inventaris digital dengan detail spesifikasi dan kondisi teknis.', 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
                ['Requests', 'Alur pengajuan peminjaman barang yang cepat dan termonitor secara real-time.', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['Tracking', 'Lacak posisi dan status penanggung jawab barang dipinjam secara akurat.', 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'],
                ['Returns', 'Sistem pengembalian otomatis dengan verifikasi kondisi barang yang akurat.', 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
            ];
            @endphp
            @foreach($features as $f)
            <div class="bg-[#1B8A1D] rounded-3xl p-8 text-white shadow-lg hover:-translate-y-1 transition-transform">
                <div class="w-12 h-12 bg-[#CDDC39] rounded-full flex items-center justify-center mb-6 text-[#1B8A1D]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f[2] }}"></path></svg>
                </div>
                <h3 class="text-xl font-bold mb-3 font-heading">{{ $f[0] }}</h3>
                <p class="text-green-50 text-[13px] leading-relaxed opacity-90">{{ $f[1] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Cara Meminjam Barang -->
<div id="tutorial" class="py-24 max-w-7xl mx-auto px-6">
    <div class="text-center mb-16">
        <h2 class="text-3xl font-bold font-heading text-gray-900 mb-3">Cara Meminjam Barang</h2>
        <p class="text-gray-500 text-sm">Ikuti 5 langkah mudah untuk mengakses aset HMIF.</p>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-6">
        @php
        $steps = [
            ['Login Akun', 'Masuk menggunakan akun member atau admin.'],
            ['Pilih Barang', 'Cari barang yang tersedia di halaman katalog.'],
            ['Isi Formulir', 'Isi formulir dengan tujuan dan durasi peminjaman.'],
            ['Persetujuan', 'Tunggu verifikasi dan status dari form WA.'],
            ['Pengambilan', 'Ambil barang yang disepakati langsung ke admin.'],
        ];
        @endphp
        @foreach($steps as $i => $s)
        <div class="flex flex-col items-center text-center">
            <div class="w-full aspect-square bg-gradient-to-br from-[#112240] to-[#0A192F] rounded-3xl mb-5 relative overflow-hidden flex items-center justify-center shadow-lg border-2 border-white">
                <div class="relative w-3/4 flex flex-col gap-2.5 z-10">
                    <div class="h-2.5 w-full bg-white/20 rounded-full"></div>
                    <div class="h-2.5 w-3/4 bg-white/15 rounded-full"></div>
                    <div class="h-5 w-1/3 bg-[#64FFDA] rounded-md mx-auto mt-2 opacity-80"></div>
                </div>
                <div class="absolute bottom-4 left-4 bg-white text-gray-900 text-[9px] font-bold px-3 py-1 rounded-full shadow-sm">STEP 0{{ $i + 1 }}</div>
            </div>
            <h4 class="font-bold text-gray-900 text-sm mb-1.5">{{ $s[0] }}</h4>
            <p class="text-xs text-gray-500 leading-relaxed px-2">{{ $s[1] }}</p>
        </div>
        @endforeach
    </div>
</div>

<!-- Pantau Aktivitas -->
<div class="bg-[#F8F9FA] py-24">
    <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-16 items-center">
        <div>
            <h2 class="text-3xl font-bold font-heading text-gray-900 mb-5 leading-tight">Pantau Aktivitas Secara<br>Langsung</h2>
            <p class="text-gray-500 mb-10 text-sm leading-relaxed">Dapatkan transparansi penuh atas ketersediaan stok dan status peminjaman terkini.</p>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-5 mb-5">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 text-[15px] mb-1">Laporan Mingguan</h4>
                    <p class="text-xs text-gray-500">Statistik aktivitas inventaris harian.</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-5">
                <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-orange-500 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 text-[15px] mb-1">Notifikasi Pengingat</h4>
                    <p class="text-xs text-gray-500">Email pengingat batas waktu kembali.</p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-6">
            <!-- Bar Chart -->
            <div class="bg-white rounded-3xl shadow-md border border-gray-50 p-6 flex flex-col justify-between">
                <h4 class="font-bold text-[10px] text-gray-400 mb-8 uppercase tracking-widest">Statistik Barang</h4>
                <div class="flex items-end gap-3 h-32 mb-4 px-2">
                    <div class="w-full bg-blue-200 rounded-t-sm h-1/2"></div>
                    <div class="w-full bg-blue-600 rounded-t-sm h-full"></div>
                    <div class="w-full bg-blue-200 rounded-t-sm h-2/5"></div>
                    <div class="w-full bg-blue-400 rounded-t-sm h-3/4"></div>
                    <div class="w-full bg-blue-200 rounded-t-sm h-1/3"></div>
                </div>
                <div class="flex justify-between text-[9px] text-gray-400 font-semibold px-1">
                    <span>MON</span><span>TUE</span><span>WED</span><span>THU</span><span>FRI</span>
                </div>
            </div>
            <!-- Activities -->
            <div class="bg-white rounded-3xl shadow-md border border-gray-50 p-6 relative sm:top-12">
                <h4 class="font-bold text-[10px] text-gray-400 mb-6 uppercase tracking-widest">Aktivitas Terakhir</h4>
                <div class="space-y-6">
                    <div class="pl-4 border-l-2 border-green-500">
                        <h5 class="text-[11px] font-bold text-gray-900 mb-0.5">Peminjaman Selesai</h5>
                        <p class="text-[9px] text-gray-400">Proyektor Epson - By Rendi</p>
                    </div>
                    <div class="pl-4 border-l-2 border-orange-400">
                        <h5 class="text-[11px] font-bold text-gray-900 mb-0.5">Menunggu Verifikasi</h5>
                        <p class="text-[9px] text-gray-400">Kamera DSLR - By Kominfo</p>
                    </div>
                    <div class="pl-4 border-l-2 border-blue-500">
                        <h5 class="text-[11px] font-bold text-gray-900 mb-0.5">Barang Tersedia</h5>
                        <p class="text-[9px] text-gray-400">Gitar Akustik - By Admin</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FAQ -->
<div id="faq" class="py-24 max-w-3xl mx-auto px-6">
    <h2 class="text-3xl font-bold font-heading text-gray-900 text-center mb-12">Frequently Asked Questions</h2>
    <div class="space-y-4">
        @foreach([
            'Siapa yang boleh meminjam barang?',
            'Berapa lama batas maksimal peminjaman?',
            'Apa yang terjadi jika barang rusak atau hilang?',
        ] as $q)
        <div class="bg-[#F8F9FA] rounded-2xl p-6 cursor-pointer hover:bg-gray-100 transition-colors flex justify-between items-center group border border-transparent hover:border-gray-200">
            <h4 class="font-semibold text-gray-900 text-[13px]">{{ $q }}</h4>
            <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-900 transition-colors shrink-0 ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
        </div>
        @endforeach
    </div>
</div>

<!-- Footer -->
<footer class="bg-[#F8F9FA] py-8 border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="flex flex-col items-center md:items-start gap-1">
            <div class="font-bold text-[13px] text-gray-900">HMIF</div>
            <div class="text-[9px] text-gray-400 uppercase tracking-widest">&copy; {{ date('Y') }} Inventory System - Tim 1</div>
        </div>
        <div class="flex flex-wrap justify-center gap-x-8 gap-y-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">
            <a href="#" class="hover:text-gray-900 transition-colors">Terms of Use</a>
            <a href="#" class="hover:text-gray-900 transition-colors">Privacy Policy</a>
            <a href="#" class="hover:text-gray-900 transition-colors">Contact Admin</a>
        </div>
    </div>
</footer>

</body>
</html>
