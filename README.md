# 🧪 Sistem Informasi Bahan Habis Pakai (BHP)

[![Laravel Version](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.x-blue.svg)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

Sistem Informasi **Bahan Habis Pakai (BHP)** adalah platform berbasis web yang dirancang untuk mendigitalisasi pengelolaan stok, inventaris, dan alur penggunaan bahan di lingkungan laboratorium atau departemen kampus. Proyek ini dikembangkan sebagai solusi modern untuk menggantikan pencatatan manual dan meningkatkan akurasi data stok.

---

## 🚀 Fitur Utama (Highlight)

*   **Dashboard Modern**: Visualisasi statistik stok, item menipis, dan antrian pengajuan secara real-time.
*   **Inventory Management**: Kelola data bahan, kategori, dan satuan dengan sistem log stok yang transparan.
*   **Workflow Pengajuan**: Alur digital dari pengajuan oleh pengguna (mahasiswa/dosen) hingga verifikasi oleh petugas (laboran).
*   **Reporting Engine**: Generate laporan pemakaian bahan dalam format PDF/Excel untuk kebutuhan administrasi bulanan.
*   **Low Stock Alert**: Notifikasi otomatis untuk bahan yang mencapai ambang batas minimum.

---

## 🛠️ Tech Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Tailwind CSS & Blade Templating
- **Database**: MySQL / MariaDB
- **Visualisasi**: Chart.js / ApexCharts
- **Laporan**: DomPDF / Laravel Excel

---

## 🗺️ Rencana Pengembangan (Roadmap TA)

Berikut adalah rencana tahapan pengembangan untuk mencapai standar **Tugas Akhir D3**:

### 📍 Milestone 1: Core System & Dashboard (Current)
- [x] Perancangan Database (Bahan, Satuan, Penggunaan).
- [x] Implementasi CRUD Master Data.
- [x] UI/UX Dashboard Modern (IndoConnex Style).
- [ ] Integrasi Data Statistik Real-time ke Dashboard.

### 📍 Milestone 2: Workflow & Logika Bisnis
- [ ] Penambahan status `Pending`, `Approved`, `Rejected` pada tabel Penggunaan.
- [ ] Fitur Pengeluaran Stok otomatis hanya setelah status `Approved`.
- [ ] Implementasi Log Stok (Audit Trail) untuk setiap perubahan qty.

### 📍 Milestone 3: Reporting & Visualisasi
- [ ] Integrasi Chart.js untuk grafik tren penggunaan bulanan.
- [ ] Fitur Export PDF Laporan Bulanan & Berita Acara.
- [ ] Sistem Filter Data laporan berdasarkan rentang tanggal.

### 📍 Milestone 4: Final Polish & Bonus
- [ ] Implementasi QR Code untuk label bahan (Akses cepat info bahan).
- [ ] Optimasi UX (SweetAlert2 untuk notifikasi & loading state).
- [ ] Dokumentasi Teknis Lengkap.

---

## ⚙️ Instalasi

1. **Clone repository**
   ```bash
   git clone https://github.com/username/sistem-informasi-bhp.git
   ```
2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```
3. **Konfigurasi Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. **Migrasi Database**
   ```bash
   php artisan migrate --seed
   ```
5. **Jalankan Aplikasi**
   ```bash
   php artisan serve
   npm run dev
   ```

---

## 📄 Lisensi
Proyek ini dilisensikan di bawah [MIT License](LICENSE).
