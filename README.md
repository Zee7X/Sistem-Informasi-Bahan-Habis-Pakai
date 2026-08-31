# 🧪 Sistem Informasi Bahan Habis Pakai (BHP) Laboratorium

<div align="center">

![BHP Lab](https://img.shields.io/badge/BHP_Lab-Politeknik_Negeri_Cilacap-2BA8A2?style=for-the-badge&logo=flask&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![React](https://img.shields.io/badge/React-19-61DAFB?style=for-the-badge&logo=react&logoColor=black)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)
![Inertia.js](https://img.shields.io/badge/Inertia.js-3-9553E9?style=for-the-badge&logo=inertia&logoColor=white)

</div>

Sistem Informasi **Bahan Habis Pakai (BHP)** adalah platform berbasis web modern yang dirancang untuk mendigitalisasi pengelolaan stok, inventaris, dan alur penggunaan bahan di lingkungan laboratorium. Sistem ini memastikan akurasi data stok melalui sistem *audit trail* yang ketat, alur persetujuan bertingkat, dan ketahanan terhadap *race condition*.

---

## 🎨 Design System

Aplikasi ini menggunakan **Flip7 Design System** — palet retro-playful teal-coral-gold yang bold, joyful, dan tactile.

### Palet Warna Utama

| Token | Hex | Fungsi |
|---|---|---|
| **Primary Teal** | `#2BA8A2` | UI utama, sidebar, avatar, progress |
| **Accent Gold** | `#FFD23F` | CTA buttons, highlights, celebrations |
| **Coral** | `#EF6C4A` | Warning, stok kritis, error states |
| **Cream** | `#FFF8E7` | Input surfaces, card backgrounds |
| **Surface Base** | `#EFF8F7` | Page background |

### Komponen Kunci

- **Buttons**: Pill-shape (`border-radius: 9999px`), min 44px height — Gold CTA untuk aksi utama
- **Cards**: Left-border 6px accent (teal/coral/gold per state) + colored glow shadow
- **Inputs**: Cream (`#FFF8E7`) background, teal focus ring
- **Status Chips**: Pill badge dengan glow shadow
- **Section Headers**: Dashed bottom border (`border-bottom: 2px dashed teal`)
- **Typography**: **Outfit** extra-bold (800) untuk judul, **Inter** untuk body

---

## 🛠️ Tech Stack

| Layer | Teknologi |
|---|---|
| **Backend** | Laravel 12 (PHP 8.2+) |
| **Frontend** | React 19 via Inertia.js |
| **Styling** | Tailwind CSS 3.4 + Flip7 Design System |
| **Font** | Outfit (display) + Inter (body) via Google Fonts |
| **Icons** | Lucide React |
| **Database** | MySQL / MariaDB |
| **Reporting** | DomPDF (PDF) & Stream CSV |
| **Alerts** | SweetAlert2 |

---

## 🔑 Hak Akses Berdasarkan Role

Sistem ini menerapkan Role-Based Access Control (RBAC) ketat dengan pembagian 3 role utama:

### 👨‍💼 1. Admin (Laboran / Pengelola Lab)
- **Dashboard**: Memantau statistik stok, total bahan, total user, jumlah antrean review, serta peringatan stok kritis.
- **Master Data**: Akses penuh CRUD untuk Bahan, Satuan, dan Kredensial User (Mahasiswa, Kjur, Admin).
- **Modul Praktikum**: Membuat dan mengelola template paket kebutuhan bahan per modul praktikum agar mahasiswa tidak perlu memilih bahan satu per satu.
- **Bahan Masuk**: Menginput restock bahan baru (menunggu approval Ketua Jurusan agar stok masuk bertambah).
- **Review Pengajuan**: 
  - `Approve`: Menyetujui permintaan mahasiswa.
  - `Reject`: Menolak dengan menyertakan alasan penolakan tertulis yang wajib diisi.
  - `Complete`: Menyelesaikan transaksi saat serah terima barang fisik ke mahasiswa (**stok terpotong otomatis di tahap ini**).
- **Stock Opname**: Penyesuaian stok manual jika terdapat selisih fisik (rusak, hilang, kadaluarsa, dll.) beserta alasan penyesuaian yang wajib diisi.
- **Laporan**: Export laporan mutasi stok per periode dalam format CSV (Excel-ready) dan PDF.
- **Log Stok**: Memantau riwayat audit log lengkap pergerakan stok masuk, keluar, dan opname.

### 🎓 2. Mahasiswa
- **Katalog Bahan**: Melihat katalog bahan habis pakai yang tersedia di laboratorium beserta lokasinya.
- **Pengajuan BHP**:
  - `Pengajuan Modul`: Memilih modul praktikum yang telah dirancang Admin, sehingga list bahan & jumlah takaran default otomatis terisi secara instan.
  - `Pengajuan Mandiri`: Memilih jenis bahan secara manual untuk kebutuhan riset mandiri atau Tugas Akhir.
  - `Detail Kelompok`: Mengisi mata kuliah, kelas, nama kelompok, jumlah anggota, tanggal pemakaian, dan keterangan.
- **Tracking Status**: Memantau status pengajuan secara real-time (`Pending`, `Approved`, `Rejected` lengkap dengan alasannya, atau `Completed`).

### 🏛️ 3. Ketua Jurusan (Kjur)
- **Dashboard Eksekutif**: Memantau tren penggunaan bahan 6 bulan terakhir, status stok laboratorium, dan permohonan belanja pending.
- **Approval Belanja**: Memberikan izin/approval terhadap transaksi bahan masuk yang diinput Admin sebelum stoknya sah masuk ke gudang.
- **Stok Bahan & Transaksi**: Memantau ketersediaan bahan secara real-time dan melihat semua riwayat transaksi pengajuan mahasiswa (*read-only*).
- **Rekap Laporan**: Mengakses laporan rekapitulasi penggunaan BHP per semester.

---

## 🔄 Alur Kerja Sistem (System Workflow)

Sistem Informasi BHP ini bekerja melalui **3 Alur Utama**:

### 1. Alur Pengajuan & Distribusi BHP (State Machine)
```
[Mahasiswa Submit] ➔ [Status: Pending Review] (Stok BELUM terpotong)
                             │
                      [Admin Review]
                        /        \
             (Reject)  /          \ (Approve)
                      ▼            ▼
             [Status: Rejected]   [Status: Approved] (Bahan siap diambil)
             (Isi Alasan Wajib)            │
                                  [Admin Klik Complete] (Saat serah terima barang)
                                           ▼
                                  [Status: Completed]
                            (Stok Terpotong & Log Stok Keluar)
```

### 2. Alur Bahan Masuk (Restock)
```
[Admin Input Bahan Masuk] ➔ [Status: Pending Kjur] (Stok belum berubah)
                                     │
                             [Kjur Review & Approve]
                                     ▼
                          [Stok Bahan Bertambah] 
                         (Tercatat Log Stok Masuk)
```

### 3. Alur Stock Opname (Penyesuaian Fisik)
```
[Admin Hitung Fisik Lab] ➔ [Temukan Selisih / Barang Rusak] 
                                     │
                    [Admin Input Opname di Sistem]
               (Pilih Jenis Penyesuaian & Alasan Wajib)
                                     ▼
                     [Stok Terkoreksi Otomatis]
                      (Tercatat Log Stok Opname)
```

---

## ⚠️ Hal-Hal Penting & Fitur Teknis Lainnya

### 1. Teknik Snapshot pada Pengajuan Items
Untuk menjaga riwayat data transaksi masa lalu tetap akurat, sistem menggunakan teknik **Snapshot** pada tabel `pengajuan_items`. Saat pengajuan dibuat, sistem menyalin nama bahan (`nama_bahan_snapshot`) dan satuan (`satuan_snapshot`). Jika di kemudian hari nama bahan pada master data diubah atau dihapus, laporan transaksi lama tetap memuat nama asli saat transaksi diajukan.

### 2. Pencegahan Race Condition (Atomic Transaction)
Proses pemotongan stok pada tahap `Complete` menggunakan database transaction (`DB::transaction`) dan penguncian baris (`lockForUpdate`). Hal ini menjamin tidak terjadi *race condition* (stok minus atau tidak konsisten) apabila terdapat beberapa admin yang memproses transaksi secara bersamaan.

### 3. Excel & PDF Generator
- Laporan Rekapitulasi dapat diekspor langsung ke format **CSV** yang sudah dilengkapi UTF-8 BOM agar terbaca dengan baik di Microsoft Excel.
- Laporan juga dapat diunduh dalam bentuk **PDF** formal yang siap dicetak dan ditandatangani oleh Laboran Pengelola Lab.

---

## ⚙️ Panduan Instalasi & Setup Lokal

Ikuti langkah-langkah di bawah ini untuk menjalankan aplikasi di komputer lokal Anda:

### 1. Clone Repository
```bash
git clone https://github.com/Zee7X/sistem-informasi-bhp.git
cd sistem-infromasi-bhp
```

### 2. Install Dependencies
```bash
# Install PHP dependencies (Laravel)
composer install

# Install Javascript dependencies (React)
npm install
```

### 3. Setup Environment
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Buka file `.env` dan sesuaikan konfigurasi database Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_bhp
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Jalankan Migrasi & Database Seeding
Jalankan perintah ini untuk membuat tabel database beserta data demo awal (roles, user seeder, satuan, dan bahan contoh):
```bash
php artisan migrate --seed
```

### 6. Jalankan Aplikasi
Anda harus menjalankan 2 terminal secara bersamaan:

- **Terminal 1: Laravel Backend Server**
  ```bash
  php artisan serve
  ```
- **Terminal 2: Vite Assets Bundler & Compiler**
  ```bash
  npm run dev
  ```

Buka browser Anda dan akses aplikasi melalui alamat yang disediakan oleh Laravel Artisan (biasanya `http://127.0.0.1:8000` atau `http://localhost:8000`).

---

## 🔑 Akun Demo Login (Default)

Gunakan akun di bawah ini setelah menjalankan seed database:

| No | Peran / Role | Email | Password | Keterangan |
|:--:|:---|:---|:---|:---|
| **1** | **Admin (Laboran)** | `admin@bhp.com` | `12345` | Kontrol Lab penuh |
| **2** | **Mahasiswa** | `mahasiswa@bhp.com` | `12345` | Membuat pengajuan |
| **3** | **Ketua Jurusan** | `ketua@bhp.com` | `12345` | Approval restock & monitoring |

> **Catatan:** Fitur *lupa password* belum diimplementasikan. Hubungi admin untuk reset password manual via database.

---

## 📁 Struktur Direktori Utama

```
sistem-infromasi-bhp/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/          # BahanMasuk, Laporan, LogStok, ModulPraktikum, Satuan, StockOpname, User
│   │   ├── Mahasiswa/      # Katalog
│   │   ├── KetuaJurusan/   # BahanMasuk, Laporan
│   │   ├── Api/            # BahanApi, ModulApi
│   │   ├── BahanController.php
│   │   ├── DashboardController.php
│   │   └── PengajuanController.php
│   └── Models/
│       ├── Bahan.php, BahanMasuk.php, LogStok.php
│       ├── ModulPraktikum.php, ModulPraktikumItem.php
│       ├── Pengajuan.php, PengajuanItem.php
│       ├── Satuan.php, StockOpname.php, User.php
├── resources/
│   ├── css/app.css         # Flip7 Design System global styles
│   └── js/
│       ├── Layouts/AppLayout.jsx   # Sidebar + Topbar (Flip7 themed)
│       ├── Components/             # Pagination, SearchableSelect
│       └── Pages/
│           ├── Auth/Login.jsx      # Flip7 login page
│           ├── Dashboard.jsx       # Unified dashboard (role-based)
│           ├── Admin/              # Bahan, BahanMasuk, Laporan, LogStok, ModulPraktikum, Pengajuan, Satuan, StockOpname, Users
│           ├── Mahasiswa/          # Katalog, Pengajuan
│           └── KetuaJurusan/       # BahanMasuk, Laporan, Transaksi
├── tailwind.config.js      # Flip7 design tokens
└── routes/web.php          # RBAC route definitions
```

---

<div align="center">
  <sub>Built with ❤️ for Politeknik Negeri Cilacap — Lab TPPL</sub>
</div>
