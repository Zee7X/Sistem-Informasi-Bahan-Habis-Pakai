# 📋 Rencana Implementasi: Sistem Manajemen BHP Laboratorium

> **Stack:** Laravel (latest) · Blade Templates · Tailwind CSS · Alpine.js (interaktivitas ringan)  
> **Lokasi Proyek:** `c:\laragon\www\sistem-infromasi-bhp`

---

## 🔍 Analisis Kondisi Saat Ini (Existing Codebase)

Berdasarkan eksplorasi folder proyek, berikut yang **sudah ada** dan yang **belum ada/perlu diperbaiki**:

### ✅ Sudah Tersedia
| Komponen | Status |
|---|---|
| Migrasi: `users`, `satuan`, `bahan`, `bahan_masuk`, `log_stok` | Ada |
| Migrasi: `penggunaan_bahan` (sebagai tabel pengajuan) | Ada, tapi perlu direfaktor |
| Model: `User`, `Bahan`, `Satuan`, `BahanMasuk`, `PenggunaanBahan` | Ada, tapi belum lengkap |
| RBAC sederhana via enum `role` di tabel `users` | Ada |
| `RoleMiddleware` untuk guard akses per-route | Ada |
| Service Layer: `BahanMasukService`, `BahanService`, `SatuanService` | Ada |
| Controller: `PengajuanController`, `DashboardController`, `BahanController` | Ada |
| Route terstruktur per-role di `web.php` | Ada |

### ❌ Belum Ada / Perlu Dibangun
| Komponen | Kebutuhan |
|---|---|
| Tabel & Model `modul_praktikum` | Fitur bundling bahan per modul |
| Tabel `modul_praktikum_items` (pivot) | Relasi modul ↔ bahan |
| Refaktor `pengajuan` → state machine 4 status | Saat ini hanya 3 status, tanpa `COMPLETED` |
| Tabel `pengajuan_items` (multi-bahan per pengajuan) | Saat ini hanya 1 bahan per pengajuan |
| Kolom `reject_reason` di `pengajuan` | Wajib saat reject |
| `StockOpnameController` & migrasi tabel | Fitur penyesuaian stok (broken/expired) |
| Fitur Laporan (Excel/PDF export) | Belum ada |
| Fitur Approval Belanja oleh Ketua Jurusan | Belum ada |
| View Blade lengkap semua halaman | Banyak yang belum dibuat |
| Seeder data awal (roles, satuan, bahan contoh) | Belum ada |

---

## ⚠️ Gap Kritis yang Harus Diperbaiki Dulu

> **[CAUTION] State Machine Salah:**  
> `PengajuanController::approve()` saat ini langsung memotong stok saat `approved`.  
> Padahal requirement menyatakan stok **HANYA** dipotong saat status → `COMPLETED`.  
> Ini adalah **bug bisnis kritis**.

> **[WARNING] Single-item Pengajuan:**  
> Tabel `penggunaan_bahan` saat ini hanya mendukung 1 bahan per pengajuan.  
> Requirement membutuhkan pengajuan berbasis **Modul Praktikum** yang berisi banyak bahan sekaligus.  
> Diperlukan **refaktor skema**.

> **[IMPORTANT] Modul Praktikum Belum Ada:**  
> Fitur krusial untuk mencegah mahasiswa mengetik nama bahan bebas.  
> Tabel dan relasi ini harus **dibangun dari awal**.

---

## 🗃️ Target Skema Database (Akhir)

```
users                    → id, name, email, password, role(enum), nim, kelas, timestamps
satuan                   → id, nama, timestamps
bahan                    → id, kode_bahan, nama_bahan, spesifikasi, stok, satuan_id(FK), minimal_stok, lokasi, keterangan, timestamps

modul_praktikum          → id, kode_modul, nama_modul, deskripsi, created_by(FK→users), is_active, timestamps
modul_praktikum_items    → id, modul_id(FK), bahan_id(FK), jumlah, timestamps

pengajuan                → id, kode_pengajuan(unique), user_id(FK), modul_id(FK nullable),
                           jenis_pengajuan(enum: modul|mandiri), mata_kuliah, kelas, kelompok,
                           tanggal_pakai, status(enum: pending_review|approved|rejected|completed),
                           reject_reason(nullable), approved_by(FK nullable), approved_at(nullable),
                           completed_by(FK nullable), completed_at(nullable), timestamps
pengajuan_items          → id, pengajuan_id(FK), bahan_id(FK nullable),
                           nama_bahan_snapshot, satuan_snapshot, jumlah, timestamps

bahan_masuk              → id, bahan_id(FK), jumlah, tanggal_masuk, pemasok, no_faktur,
                           harga_satuan(nullable), approved_by_kjur(FK nullable),
                           status_kjur(enum: pending|approved), keterangan, created_by(FK), timestamps
stock_opname             → id, bahan_id(FK), stok_sebelum, stok_sesuai, selisih(computed),
                           alasan(required), jenis_penyesuaian(enum), created_by(FK), timestamps
log_stok                 → id, bahan_id(FK), tanggal, jenis(enum: masuk|keluar|adjust|opname),
                           jumlah, stok_sesudah, reference_table, reference_id,
                           keterangan, created_by(FK), created_at
```

### ERD Relasi Kunci
```
users ──< pengajuan (sebagai pemohon)
users ──< pengajuan (sebagai approver/completer)
pengajuan >── modul_praktikum (optional)
pengajuan ──< pengajuan_items >── bahan
modul_praktikum ──< modul_praktikum_items >── bahan
bahan >── satuan
bahan ──< bahan_masuk
bahan ──< stock_opname
bahan ──< log_stok
```

---

## 🔄 State Machine Pengajuan (Alur BHP)

```
[Mahasiswa Submit]
        │
        ▼
  ┌─────────────────┐
  │  PENDING_REVIEW  │ ◄── Stok BELUM dipotong
  └─────────────────┘
          │
     Admin Review
          │
     ┌────┴────┐
     ▼         ▼
┌──────────┐ ┌──────────┐
│ APPROVED │ │ REJECTED │ ◄── Wajib isi reject_reason
└──────────┘ └──────────┘
      │           (Terminal State)
      │ Admin serahkan bahan ke mahasiswa
      ▼
┌──────────┐
│COMPLETED │ ◄── TRIGGER: Stok dipotong di sini (DB Transaction + lockForUpdate)
└──────────┘
  (Terminal State)
```

> **[IMPORTANT] Soft Reserve (Opsional):**  
> Saat `APPROVED`, stok bisa di-soft-reserve agar tidak diklaim pengajuan lain.  
> Implementasi: filter stok efektif = `stok - SUM(jumlah WHERE status = 'approved')`

---

## 📦 Rencana Tugas Per Fase

---

### FASE 1 — Fondasi Database & Model (Prioritas Tertinggi)

#### Migrasi yang Perlu Dibuat/Dimodifikasi

**[MODIFY]** `database/migrations/0001_01_01_000000_create_users_table.php`
- Tambah kolom: `program_studi`, `angkatan`, `no_telp`

**[NEW]** `database/migrations/xxxx_create_modul_praktikum_table.php`
```php
Schema::create('modul_praktikum', function (Blueprint $table) {
    $table->id();
    $table->string('kode_modul', 50)->unique();
    $table->string('nama_modul', 200);
    $table->text('deskripsi')->nullable();
    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

**[NEW]** `database/migrations/xxxx_create_modul_praktikum_items_table.php`
```php
Schema::create('modul_praktikum_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('modul_id')->constrained('modul_praktikum')->cascadeOnDelete();
    $table->foreignId('bahan_id')->constrained('bahan')->cascadeOnDelete();
    $table->decimal('jumlah', 10, 2); // desimal untuk mendukung 3.5g dsb
    $table->timestamps();
    $table->unique(['modul_id', 'bahan_id']); // 1 bahan per modul
});
```

**[NEW]** `database/migrations/xxxx_create_pengajuan_table.php` ← Gantikan `penggunaan_bahan`
```php
Schema::create('pengajuan', function (Blueprint $table) {
    $table->id();
    $table->string('kode_pengajuan', 30)->unique(); // format: BHP-2026-0001
    $table->foreignId('user_id')->constrained('users');
    $table->foreignId('modul_id')->nullable()->constrained('modul_praktikum')->nullOnDelete();
    $table->enum('jenis', ['modul', 'mandiri'])->default('modul');
    $table->string('mata_kuliah', 200)->nullable();
    $table->string('kelas', 100)->nullable();
    $table->string('kelompok', 100)->nullable();
    $table->date('tanggal_pakai');
    $table->text('keterangan')->nullable();
    $table->enum('status', ['pending_review', 'approved', 'rejected', 'completed'])
          ->default('pending_review');
    $table->text('reject_reason')->nullable();
    $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
    $table->dateTime('approved_at')->nullable();
    $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
    $table->dateTime('completed_at')->nullable();
    $table->timestamps();

    $table->index(['user_id', 'status']);
    $table->index('tanggal_pakai');
});
```

**[NEW]** `database/migrations/xxxx_create_pengajuan_items_table.php`
```php
Schema::create('pengajuan_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('pengajuan_id')->constrained('pengajuan')->cascadeOnDelete();
    $table->foreignId('bahan_id')->nullable()->constrained('bahan')->nullOnDelete();
    // Snapshot nama & satuan saat pengajuan agar tidak berubah jika master diedit
    $table->string('nama_bahan_snapshot', 255);
    $table->string('satuan_snapshot', 50);
    $table->decimal('jumlah', 10, 2);
    $table->timestamps();
});
```

**[NEW]** `database/migrations/xxxx_create_stock_opname_table.php`
```php
Schema::create('stock_opname', function (Blueprint $table) {
    $table->id();
    $table->foreignId('bahan_id')->constrained('bahan')->cascadeOnDelete();
    $table->integer('stok_sebelum');
    $table->integer('stok_sesuai');
    $table->integer('selisih')->storedAs('stok_sesuai - stok_sebelum');
    $table->text('alasan'); // WAJIB DIISI
    $table->enum('jenis_penyesuaian', ['rusak', 'kadaluarsa', 'hilang', 'koreksi_lain']);
    $table->foreignId('created_by')->constrained('users');
    $table->timestamps();
});
```

**[MODIFY]** `database/migrations/2026_04_17_145854_create_bahan_masuks_table.php`
- Tambah kolom: `no_faktur`, `harga_satuan`, `approved_by_kjur (FK)`, `status_kjur (enum: pending|approved)`, `created_by (FK)`

**[MODIFY]** `database/migrations/2025_10_23_081021_create_log_stok_table.php`
- Tambah kolom: `reference_table (string)` untuk mengetahui sumber log (pengajuan/bahan_masuk/stock_opname)
- Tambah enum value `opname` ke kolom `jenis`

#### Model yang Perlu Dibuat/Dimodifikasi

**[NEW]** `app/Models/Pengajuan.php`
- Relasi: `belongsTo(User)`, `belongsTo(ModulPraktikum)`, `hasMany(PengajuanItem)`
- Relasi: `belongsTo(User, 'approved_by')`, `belongsTo(User, 'completed_by')`
- Cast: `tanggal_pakai → date`, `approved_at/completed_at → datetime`
- Helper methods: `isPendingReview()`, `isApproved()`, `isRejected()`, `isCompleted()`
- Helper methods: `canBeApproved()`, `canBeCompleted()`, `canBeRejected()`

**[NEW]** `app/Models/PengajuanItem.php`
- Relasi: `belongsTo(Pengajuan)`, `belongsTo(Bahan)`

**[NEW]** `app/Models/ModulPraktikum.php`
- Relasi: `hasMany(ModulPraktikumItem)`, `belongsTo(User, 'created_by')`
- Scope: `active()` untuk filter `is_active = true`

**[NEW]** `app/Models/ModulPraktikumItem.php`
- Relasi: `belongsTo(ModulPraktikum)`, `belongsTo(Bahan)`

**[NEW]** `app/Models/StockOpname.php`
- Relasi: `belongsTo(Bahan)`, `belongsTo(User, 'created_by')`

**[MODIFY]** `app/Models/User.php`
- Tambah: `fillable` untuk `role`, `nim`, `kelas`, `program_studi`, `angkatan`, `no_telp`
- Tambah: helper methods `isAdmin()`, `isMahasiswa()`, `isKetuaJurusan()`
- Tambah: relasi `hasMany(Pengajuan)`

**[MODIFY]** `app/Models/Bahan.php`
- Tambah: relasi `hasMany(StockOpname)`, `hasMany(LogStok)`, `hasMany(BahanMasuk)`
- Tambah: accessor `isKritis()` (stok <= minimal_stok)

---

### FASE 2 — Service Layer & Business Logic

**[NEW]** `app/Services/PengajuanService.php`
- `store(array $data, User $user): Pengajuan` — buat pengajuan + items, generate kode
- `approve(Pengajuan $pengajuan, User $admin): void` — ubah status ke `approved`
- `reject(Pengajuan $pengajuan, User $admin, string $reason): void` — ubah status ke `rejected`
- `complete(Pengajuan $pengajuan, User $admin): void`
  - Ubah ke `completed`
  - Potong stok per-item dengan `lockForUpdate()` di dalam `DB::transaction()`
  - Catat ke `log_stok` untuk setiap item
- `generateKodePengajuan(): string` — format `BHP-{YYYY}-{0001}`, atomic & race-condition-safe

**[MODIFY]** `app/Services/BahanMasukService.php`
- Tambah: log ke `log_stok` setiap kali stok bertambah
- Tambah: set `status_kjur = pending` jika butuh persetujuan Ketua Jurusan

**[NEW]** `app/Services/StockOpnameService.php`
- `adjust(array $data, User $user): StockOpname`
  - Simpan record opname
  - Update stok bahan
  - Catat ke `log_stok` dengan jenis `opname`

**[NEW]** `app/Services/LaporanService.php`
- `generateTransaksiExcel(array $filters): BinaryFileResponse`
- `generateTransaksiPdf(array $filters): Response`
- `generateRekap(string $semester, int $tahun): array`

---

### FASE 3 — Controller Layer

**[MODIFY]** `app/Http/Controllers/PengajuanController.php`
- Refaktor total: inject dan gunakan `PengajuanService`
- Tambah method: `complete(Pengajuan $pengajuan)` — admin serahkan bahan ke mahasiswa
- **Fix bug:** stok dipotong di `complete()`, **BUKAN** di `approve()`
- Tambah: validasi `reject_reason` wajib saat reject
- Tambah: method `show(Pengajuan $pengajuan)` untuk detail

**[NEW]** `app/Http/Controllers/Admin/ModulPraktikumController.php`
- CRUD penuh: `index`, `create`, `store`, `edit`, `update`, `destroy`
- `storeItem(ModulPraktikum $modul, Request $request)` — tambah bahan ke modul
- `destroyItem(ModulPraktikum $modul, ModulPraktikumItem $item)` — hapus bahan dari modul

**[NEW]** `app/Http/Controllers/Admin/StockOpnameController.php`
- `index()` — list semua penyesuaian stok dengan filter
- `store()` — lakukan opname, delegate ke `StockOpnameService`

**[NEW]** `app/Http/Controllers/Admin/LaporanController.php`
- `index()` — halaman laporan dengan filter
- `export(Request $request)` — delegate ke `LaporanService`

**[NEW]** `app/Http/Controllers/KetuaJurusan/DashboardController.php`
- Data chart penggunaan per bahan/per bulan untuk visualisasi analitik

**[NEW]** `app/Http/Controllers/KetuaJurusan/LaporanController.php`
- `rekap()` — laporan rekapitulasi per semester
- `exportRekap()` — export ke Excel/PDF

**[NEW]** `app/Http/Controllers/KetuaJurusan/BahanMasukController.php`
- `index()` — list bahan masuk pending approval
- `approve(BahanMasuk $masuk)` — setujui restock

**[MODIFY]** `app/Http/Controllers/DashboardController.php`
- Pisah logic per role menjadi method private terpisah: `adminStats()`, `mahasiswaStats()`, `kjurStats()`
- Tambah data chart usage per bahan untuk Ketua Jurusan

**[NEW]** `app/Http/Controllers/Api/ModulApiController.php`
- `GET /api/modul/{id}/items` — return JSON items modul untuk auto-populate form pengajuan

---

### FASE 4 — Routes & Middleware

**[MODIFY]** `routes/web.php`

```php
// =============================================
// ADMIN ROUTES
// =============================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Pengajuan
    Route::get('pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
    Route::get('pengajuan/{pengajuan}', [PengajuanController::class, 'show'])->name('pengajuan.show');
    Route::post('pengajuan/{pengajuan}/approve', [PengajuanController::class, 'approve'])->name('pengajuan.approve');
    Route::post('pengajuan/{pengajuan}/reject', [PengajuanController::class, 'reject'])->name('pengajuan.reject');
    Route::post('pengajuan/{pengajuan}/complete', [PengajuanController::class, 'complete'])->name('pengajuan.complete');

    // Modul Praktikum
    Route::resource('modul-praktikum', ModulPraktikumController::class);
    Route::post('modul-praktikum/{modul}/items', [ModulPraktikumController::class, 'storeItem'])->name('modul-praktikum.items.store');
    Route::delete('modul-praktikum/{modul}/items/{item}', [ModulPraktikumController::class, 'destroyItem'])->name('modul-praktikum.items.destroy');

    // Stock Opname
    Route::get('stock-opname', [StockOpnameController::class, 'index'])->name('stock-opname.index');
    Route::post('stock-opname', [StockOpnameController::class, 'store'])->name('stock-opname.store');

    // Laporan
    Route::get('laporan', [Admin\LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/export', [Admin\LaporanController::class, 'export'])->name('laporan.export');

    // Master Data (existing, keep)
    Route::resource('satuan', SatuanController::class)->except(['show', 'create', 'edit']);
    Route::resource('bahan', BahanController::class)->except(['show', 'create', 'edit']);
    Route::resource('bahan-masuk', BahanMasukController::class)->except(['show', 'edit', 'update']);
    Route::get('users', [AdminController::class, 'users'])->name('users');
});

// =============================================
// MAHASISWA ROUTES
// =============================================
Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('katalog', [KatalogController::class, 'index'])->name('katalog.index');
    Route::get('pengajuan', [PengajuanController::class, 'myIndex'])->name('pengajuan.index');
    Route::get('pengajuan/create', [PengajuanController::class, 'create'])->name('pengajuan.create');
    Route::post('pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');
    Route::get('pengajuan/{pengajuan}', [PengajuanController::class, 'show'])->name('pengajuan.show');
});

// =============================================
// KETUA JURUSAN ROUTES
// =============================================
Route::middleware(['auth', 'role:ketua_jurusan'])->prefix('kjur')->name('kjur.')->group(function () {
    Route::get('dashboard', [KJurDashboardController::class, 'index'])->name('dashboard');
    Route::get('transaksi', [PengajuanController::class, 'index'])->name('transaksi.index');   // View only
    Route::get('bahan', [BahanController::class, 'index'])->name('bahan.index');               // View only
    Route::get('laporan/rekap', [KJurLaporanController::class, 'rekap'])->name('laporan.rekap');
    Route::get('laporan/rekap/export', [KJurLaporanController::class, 'exportRekap'])->name('laporan.rekap.export');
    Route::get('bahan-masuk', [KJurBahanMasukController::class, 'index'])->name('bahan-masuk.index');
    Route::post('bahan-masuk/{masuk}/approve', [KJurBahanMasukController::class, 'approve'])->name('bahan-masuk.approve');
});

// =============================================
// API ROUTES (internal, auth only)
// =============================================
Route::middleware('auth')->prefix('api')->name('api.')->group(function () {
    Route::get('bahan/search', [BahanApiController::class, 'search'])->name('bahan.search');
    Route::get('modul/{modul}/items', [ModulApiController::class, 'items'])->name('modul.items');
});
```

---

### FASE 5 — Views (Blade Templates)

#### Layout & Komponen Shared

**[NEW]** `resources/views/layouts/app.blade.php`
- Sidebar responsif dengan navigasi per-role (hidden/show berdasarkan `auth()->user()->role`)
- Flash message (success/error/warning) dengan auto-dismiss
- Alpine.js untuk toggle sidebar mobile

**[NEW]** `resources/views/components/`
- `stat-card.blade.php` — kartu statistik dashboard (icon, nilai, label, trend)
- `status-badge.blade.php` — badge status pengajuan berwarna per-status
- `alert.blade.php` — alert flash message dengan icon
- `empty-state.blade.php` — tampilan saat data kosong

#### Halaman per Role

**Admin (Laboran):**

| File | Deskripsi |
|---|---|
| `views/dashboard.blade.php` | Stok kritis alert + pending requests + grafik tren |
| `views/admin/pengajuan/index.blade.php` | List semua pengajuan + tombol approve/reject/complete |
| `views/admin/pengajuan/show.blade.php` | Detail pengajuan per-item |
| `views/admin/bahan-masuk/index.blade.php` | Input & list bahan masuk |
| `views/admin/stock-opname/index.blade.php` | Form & list penyesuaian stok |
| `views/admin/modul-praktikum/index.blade.php` | List semua modul |
| `views/admin/modul-praktikum/create.blade.php` | Buat modul + tambah items (Alpine.js dynamic) |
| `views/admin/modul-praktikum/edit.blade.php` | Edit modul + kelola items |
| `views/admin/bahan/index.blade.php` | CRUD bahan (modal form) |
| `views/admin/satuan/index.blade.php` | CRUD satuan (modal form) |
| `views/admin/users/index.blade.php` | Manage users |
| `views/admin/laporan/index.blade.php` | Filter rentang tanggal + tombol export |

**Mahasiswa (Student):**

| File | Deskripsi |
|---|---|
| `views/mahasiswa/dashboard.blade.php` | Summary: total/pending/approved/rejected |
| `views/mahasiswa/katalog/index.blade.php` | Katalog bahan + stok tersedia (read only) |
| `views/mahasiswa/pengajuan/index.blade.php` | Riwayat pengajuan dengan status badge |
| `views/mahasiswa/pengajuan/create.blade.php` | Form buat pengajuan (lihat catatan UX di bawah) |
| `views/mahasiswa/pengajuan/show.blade.php` | Detail status + daftar item + reject reason |

**Ketua Jurusan:**

| File | Deskripsi |
|---|---|
| `views/ketua-jurusan/dashboard.blade.php` | Chart: usage per bahan per bulan (Chart.js) |
| `views/ketua-jurusan/transaksi/index.blade.php` | List pengajuan (view only, no actions) |
| `views/ketua-jurusan/bahan/index.blade.php` | List bahan + stok (view only) |
| `views/ketua-jurusan/laporan/rekap.blade.php` | Filter semester/tahun + tabel rekap + export |
| `views/ketua-jurusan/bahan-masuk/index.blade.php` | List pending restock + tombol approve |

#### Form Pengajuan Mahasiswa — Critical UX Flow

```
Step 1: Pilih Jenis Pengajuan
        ├── [ ] Modul Praktikum (Praktikum Mata Kuliah)
        └── [ ] Mandiri / Riset Independen

Step 2a (jika Modul):
        → Dropdown pilih Modul
        → Alpine.js fetch: GET /api/modul/{id}/items
        → Auto-populate tabel bahan + jumlah (read-only, sesuai modul)
        → Input: Mata Kuliah, Kelas, Kelompok, Tanggal Pakai

Step 2b (jika Mandiri):
        → Pilih Bahan dari dropdown (hanya yg stok > 0)
        → Input jumlah (validasi js: tidak melebihi stok)
        → Bisa tambah bahan (dynamic row via Alpine.js)
        → Input: Keterangan/Tujuan Riset, Tanggal Pakai

Step 3: Review & Submit
        → Tampilkan ringkasan
        → Konfirmasi submit
```

---

### FASE 6 — Seeders & Testing

**[NEW]** `database/seeders/DatabaseSeeder.php` — orchestrate semua seeder

**[NEW]** `database/seeders/UserSeeder.php`
```
admin@lab.ac.id     / password → role: admin
mahasiswa@test.ac.id / password → role: mahasiswa, nim: 12345678
kjur@lab.ac.id      / password → role: ketua_jurusan
```

**[NEW]** `database/seeders/SatuanSeeder.php`
```
ml, gram (g), liter (L), mg, kg, butir, lembar, botol, unit
```

**[NEW]** `database/seeders/BahanSeeder.php`
```
10+ bahan kimia contoh (NaCl, HCl, Etanol, dll) dengan stok awal dan minimal_stok
```

**[NEW]** `database/seeders/ModulPraktikumSeeder.php`
```
3-5 modul contoh, masing-masing berisi 3-5 item bahan
```

---

## 🗺️ Urutan Pengerjaan yang Direkomendasikan

```
FASE 1: Database & Models
    └─► FASE 2: Service Layer
            └─► FASE 3: Controllers
                    └─► FASE 4: Routes
                            └─► FASE 5: Views
                                    └─► FASE 6: Seeders & Verify
```

> **[TIP] Mulai dari sini:**  
> Fase 1 harus selesai dulu sebelum yang lain.  
> Migrasi `pengajuan` baru dan `modul_praktikum` adalah fondasi semua fitur.

---

## 📐 Konvensi Kode

| Aspek | Konvensi |
|---|---|
| Nama tabel | `snake_case` plural (kecuali `bahan`, `satuan` yang sudah ada) |
| Nama model | `PascalCase` singular |
| Nama controller | `PascalCase` + `Controller` suffix |
| Service class | `PascalCase` + `Service` suffix |
| Route names | `role.resource.action` (contoh: `admin.pengajuan.approve`) |
| Status enum | `snake_case` (contoh: `pending_review`, `completed`) |
| Kode pengajuan | Format `BHP-{YEAR}-{4-DIGIT}` (contoh: `BHP-2026-0042`) |
| Form Request | `PascalCase` + `Request` suffix, simpan di `app/Http/Requests/` |

---

## 🔐 Matriks Akses Per Fitur

| Fitur | Admin | Mahasiswa | Ketua Jurusan |
|---|:---:|:---:|:---:|
| Dashboard (role-specific) | ✅ | ✅ | ✅ |
| Master Bahan (CRUD) | ✅ | View Only | View Only |
| Master Satuan (CRUD) | ✅ | ❌ | ❌ |
| Master Users (CRUD) | ✅ | ❌ | ❌ |
| Modul Praktikum (CRUD) | ✅ | ❌ | ❌ |
| Katalog Bahan | ✅ | ✅ | ❌ |
| Buat Pengajuan | ❌ | ✅ | ❌ |
| Lihat Semua Pengajuan | ✅ | Milik Sendiri | ✅ |
| Approve Pengajuan | ✅ | ❌ | ❌ |
| Reject Pengajuan | ✅ | ❌ | ❌ |
| Complete Pengajuan | ✅ | ❌ | ❌ |
| Input Bahan Masuk | ✅ | ❌ | ❌ |
| Approve Bahan Masuk | ❌ | ❌ | ✅ |
| Stock Opname | ✅ | ❌ | ❌ |
| Laporan Transaksi (Export) | ✅ | ❌ | ✅ |
| Laporan Rekapitulasi | ❌ | ❌ | ✅ |

---

## 📦 Dependensi yang Perlu Dipasang

```bash
# Export Excel
composer require maatwebsite/excel

# Export PDF
composer require barryvdh/laravel-dompdf

# Query debugging (development only)
composer require barryvdh/laravel-debugbar --dev
```

---

## 🔍 Checklist Verifikasi Per Fase

| Fase | Perintah / Cara Verifikasi |
|---|---|
| Fase 1 | `php artisan migrate:fresh` tanpa error; semua relasi model bisa di-call via `tinker` |
| Fase 2 | Service bisa dipanggil dari `tinker`; logika state machine tidak bisa loncat status |
| Fase 3 | Semua route terdaftar di `php artisan route:list`; response 200 di browser |
| Fase 4 | Role yang salah mendapat response 403 (uji manual dengan 3 akun berbeda) |
| Fase 5 | Semua form bisa disubmit; data tersimpan di DB; flash message muncul |
| Fase 6 | `php artisan db:seed` berhasil; login ke-3 role berfungsi tanpa error |
