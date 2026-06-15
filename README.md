# 🧪 Sistem Informasi Bahan Habis Pakai (BHP) Laboratorium

Sistem Informasi **Bahan Habis Pakai (BHP)** adalah platform berbasis web modern yang dirancang untuk mendigitalisasi pengelolaan stok, inventaris, dan alur penggunaan bahan di lingkungan laboratorium. Sistem ini memastikan akurasi data stok melalui sistem *audit trail* yang ketat, alur persetujuan bertingkat, dan ketahanan terhadap *race condition*.

---

## 🛠️ Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: React.js via Inertia.js
- **Styling**: Tailwind CSS
- **Icons**: Lucide React
- **Database**: MySQL / MariaDB
- **Reporting**: DomPDF (PDF) & Stream CSV

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

---
🤖 *Generated with Claude Code*
