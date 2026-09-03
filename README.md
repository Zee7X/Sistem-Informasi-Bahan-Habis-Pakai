<div align="center">

<img src="public/images/tppl.png" alt="TPPL" width="120" />

# 🧪 Sistem Informasi Bahan Habis Pakai

### Manajemen BHP Laboratorium

Sistem berbasis web untuk mengelola stok, pengajuan, distribusi, dan pelaporan bahan habis pakai laboratorium.

<br>

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel\&logoColor=white)](https://laravel.com/)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php\&logoColor=white)](https://www.php.net/)
[![React](https://img.shields.io/badge/React-19-61DAFB?logo=react\&logoColor=black)](https://react.dev/)
[![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?logo=mysql\&logoColor=white)](https://www.mysql.com/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.4-06B6D4?logo=tailwindcss\&logoColor=white)](https://tailwindcss.com/)

</div>

---

## 📋 Tentang Project

**Sistem Informasi Bahan Habis Pakai** adalah aplikasi untuk membantu pengelolaan BHP laboratorium secara terpusat.

Sistem menangani proses mulai dari pencatatan bahan, pengajuan mahasiswa, approval, distribusi, stock opname, hingga pelaporan.

---

## ✨ Fitur Utama

* 📦 Manajemen bahan dan stok
* 🧪 Modul kebutuhan praktikum
* 🎓 Pengajuan bahan oleh mahasiswa
* ✅ Approval dan penolakan pengajuan
* 📥 Pencatatan bahan masuk
* 📋 Stock opname
* 📝 Riwayat dan log perubahan stok
* 📊 Laporan penggunaan bahan
* 👥 Role-based access
* 📄 Export laporan PDF / CSV

---

## 👥 Role Pengguna

### 👨‍💼 Admin / Laboran

* Mengelola bahan dan satuan
* Mengelola pengguna
* Membuat modul praktikum
* Review pengajuan mahasiswa
* Mengelola bahan masuk
* Melakukan stock opname
* Melihat laporan dan log stok

### 🎓 Mahasiswa

* Melihat katalog bahan
* Membuat pengajuan
* Mengajukan berdasarkan modul praktikum
* Membuat pengajuan mandiri
* Melihat status dan riwayat pengajuan

### 🏛️ Ketua Jurusan

* Monitoring data
* Review bahan masuk
* Approval restock
* Melihat laporan

---

## 🔄 Alur Sistem

```text
Mahasiswa
    │
    ▼
Pengajuan Bahan
    │
    ▼
Review Laboran
    │
    ├── Ditolak ❌
    │
    └── Disetujui ✅
             │
             ▼
        Serah Terima
             │
             ▼
       Stok Berkurang
             │
             ▼
        Log Tercatat
```

---

## 🛠️ Tech Stack

<div align="center">

<img src="https://skillicons.dev/icons?i=laravel,php,react,mysql,tailwind,vite,git,github" />

</div>

<br>

| Teknologi    | Penggunaan                |
| ------------ | ------------------------- |
| Laravel 12   | Backend                   |
| PHP 8.2+     | Server-side               |
| React 19     | Frontend                  |
| Inertia.js   | Integrasi Laravel & React |
| MySQL        | Database                  |
| Tailwind CSS | Styling                   |
| Vite         | Build Tool                |
| DomPDF       | Export PDF                |

---

## 🚀 Instalasi

Clone repository:

```bash
git clone https://github.com/Zee7X/Sistem-Informasi-Bahan-Habis-Pakai.git
cd Sistem-Informasi-Bahan-Habis-Pakai
```

Install dependency:

```bash
composer install
npm install
```

Buat environment:

```bash
cp .env.example .env
php artisan key:generate
```

Atur database pada `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_bhp
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migration dan seeder:

```bash
php artisan migrate --seed
```

Jalankan aplikasi:

```bash
composer run dev
```

Atau secara terpisah:

```bash
php artisan serve
npm run dev
```

---

## 🌐 Live Demo

Aplikasi sudah ter-deploy dan dapat diakses publik di:

**➡️ [https://bhp-lab.onrender.com](https://bhp-lab.onrender.com)**

> [!NOTE]
> Hosted di Render (free plan) + Aiven MySQL. Instance free tier akan *spin down* setelah ±15 menit tanpa aktivitas, jadi akses pertama bisa memakan waktu hingga ±50 detik. Sesudah itu aplikasi berjalan normal.

---

## 🔑 Akun Demo

Gunakan akun berikut untuk mencoba live demo:

| Role            | Email               | Password |
| --------------- | ------------------- | -------- |
| Admin / Laboran | `admin@bhp.com`     | `12345`  |
| Mahasiswa       | `mahasiswa@bhp.com` | `12345`  |
| Ketua Jurusan   | `ketua@bhp.com`     | `12345`  |

> Akun di atas ditujukan untuk development/demo.

---

## 👨‍💻 Developer

<div align="center">

Dikembangkan dan dikelola oleh

### Zee7X

[![GitHub](https://img.shields.io/badge/GitHub-Zee7X-181717?style=for-the-badge\&logo=github\&logoColor=white)](https://github.com/Zee7X)

<br>

**Sistem Informasi Bahan Habis Pakai**

Built with ❤️ using Laravel & React

</div>
