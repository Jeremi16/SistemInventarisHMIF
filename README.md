# Sistem Manajemen Inventaris HMIF (Himpunan Mahasiswa Informatika)

Sistem ini dirancang untuk mengelola aset dan peralatan Himpunan Mahasiswa Informatika (HMIF) Institut Teknologi Sumatera secara terpusat, transparan, dan akuntabel. Sistem ini memungkinkan pengurus untuk memantau ketersediaan barang secara real-time dan mengelola proses peminjaman dengan lebih efisien.

## 🚀 Tech Stack
Sistem ini dikembangkan menggunakan teknologi modern untuk memastikan performa dan keamanan optimal:
* **Framework:** Laravel 13 (Latest Stable)
* **Frontend:** Tailwind CSS (Custom Theme)
* **Database:** PostgreSQL (Recommended for Scalability)
* **Tools:** Laravel Breeze (Authentication), Blade Components

## ✨ Fitur Utama (Berdasarkan PRD)
* **Manajemen Barang (CRUD):** Pengelolaan data barang masuk dan keluar lengkap dengan SKU dan foto.
* **Sistem Peminjaman & Pengembalian:** Alur peminjaman dengan status real-time (Pending, Approved, Rejected, dsb).
* **Konfirmasi WhatsApp:** Notifikasi otomatis yang mengarah ke kontak Sherizka sebagai admin untuk verifikasi cepat.
* **Sistem Denda:** Penghitungan denda otomatis jika pengembalian melewati batas waktu yang ditentukan.
* **Dashboard Statistik:** Ringkasan total barang, barang tersedia, dan peminjaman jatuh tempo.
* **Export Laporan:** Kemampuan mengunduh laporan dalam format PDF atau Excel untuk audit organisasi.

## 🛠️ Panduan Instalasi
Bagi anggota tim (Backend Developer), ikuti langkah berikut untuk menjalankan proyek di lokal:

1.  **Clone Repository:**
    ```bash
    git clone https://github.com/syahiddd/sistem-inventaris-hmif.git
    cd sistem-inventaris-hmif
    ```

2.  **Install Dependencies:**
    ```bash
    composer install
    npm install
    ```

3.  **Konfigurasi Environment:**
    Salin file `.env.example` menjadi `.env` dan sesuaikan pengaturan database Anda.
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4.  **Migrasi Database:**
    ```bash
    php artisan migrate --seed
    ```

5.  **Jalankan Server:**
    ```bash
    php artisan serve
    npm run dev
    ```
---
*Dokumen ini mengacu pada Product Requirements Document (PRD) Versi 1.0.0 tanggal 8 Maret 2026.*