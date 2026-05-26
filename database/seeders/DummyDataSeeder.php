<?php

namespace Database\Seeders;

use App\Models\Bahan;
use App\Models\BahanMasuk;
use App\Models\LogStok;
use App\Models\ModulPraktikum;
use App\Models\Pengajuan;
use App\Models\PengajuanItem;
use App\Models\StockOpname;
use App\Models\User;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin      = User::where('role', 'admin')->first();
        $kjur       = User::where('role', 'ketua_jurusan')->first();
        $mahasiswas = User::where('role', 'mahasiswa')->get();
        $bahans     = Bahan::with('satuan')->get();
        $moduls     = ModulPraktikum::with('items')->where('is_active', true)->get();

        if ($bahans->isEmpty() || $mahasiswas->isEmpty()) {
            $this->command->warn('Tidak ada data bahan atau mahasiswa.');
            return;
        }

        // ──────────────────────────────────────────────────────────
        // 1. BAHAN MASUK — Riwayat pembelian (5 transaksi)
        // ──────────────────────────────────────────────────────────
        $pembelian = [
            ['pemasok' => 'PT. Kimia Farma',  'no_faktur' => 'INV-2026-0312', 'bulan_lalu' => 4],
            ['pemasok' => 'CV. Reagent Indo',  'no_faktur' => 'INV-2026-0401', 'bulan_lalu' => 3],
            ['pemasok' => 'PT. Kimia Farma',  'no_faktur' => 'INV-2026-0502', 'bulan_lalu' => 2],
            ['pemasok' => 'UD. Lab Supply',    'no_faktur' => 'INV-2026-0601', 'bulan_lalu' => 1],
            ['pemasok' => 'CV. Reagent Indo',  'no_faktur' => 'INV-2026-0703', 'bulan_lalu' => 0],
        ];

        foreach ($pembelian as $p) {
            $bahan  = $bahans->random();
            $jumlah = rand(50, 200);
            $tgl    = now()->subMonths($p['bulan_lalu'])->subDays(rand(1, 15));

            $masuk = BahanMasuk::create([
                'bahan_id'         => $bahan->id,
                'jumlah'           => $jumlah,
                'tanggal_masuk'    => $tgl,
                'pemasok'          => $p['pemasok'],
                'no_faktur'        => $p['no_faktur'],
                'harga_satuan'     => rand(5000, 150000),
                'keterangan'       => 'Pembelian rutin ' . $tgl->format('M Y'),
                'status_kjur'      => 'approved',
                'approved_by_kjur' => $kjur?->id,
                'created_by'       => $admin?->id,
            ]);

            $stokSebelum = $bahan->stok;
            $bahan->increment('stok', $jumlah);
            $bahan->refresh();

            LogStok::create([
                'bahan_id'        => $bahan->id,
                'tanggal'         => $tgl,
                'jenis'           => 'masuk',
                'jumlah'          => $jumlah,
                'stok_sebelum'    => $stokSebelum,
                'stok_sesudah'    => $bahan->stok,
                'reference_id'    => $masuk->id,
                'reference_table' => 'bahan_masuk',
                'keterangan'      => 'Stok masuk dari ' . $p['pemasok'],
                'created_by'      => $admin?->id,
            ]);
        }

        // ──────────────────────────────────────────────────────────
        // 2. PENGAJUAN — berbagai status
        // ──────────────────────────────────────────────────────────
        $matkul    = ['Kimia Dasar I', 'Kimia Dasar II', 'Biologi Sel', 'Fisika Eksperimen', 'Kimia Organik', 'Analisis Instrumen'];
        $kelas     = ['TI-22-A', 'TI-22-B', 'TK-23-A', 'TK-23-B', 'TL-22-A'];
        $counter   = 1;
        $statusList = ['completed', 'completed', 'completed', 'approved', 'pending_review', 'pending_review', 'rejected'];

        foreach ($statusList as $status) {
            $mahasiswa     = $mahasiswas->random();
            $jenisAjuan    = ($moduls->isNotEmpty() && rand(0, 1)) ? 'modul' : 'mandiri';
            $modul         = ($jenisAjuan === 'modul') ? $moduls->random() : null;
            $tglDibuat     = now()->subDays(rand(5, 90));

            $pengajuan = Pengajuan::create([
                'kode_pengajuan' => 'BHP-2026-' . str_pad($counter++, 4, '0', STR_PAD_LEFT),
                'user_id'        => $mahasiswa->id,
                'modul_id'       => $modul?->id,
                'jenis'          => $jenisAjuan,
                'mata_kuliah'    => $matkul[array_rand($matkul)],
                'kelas'          => $kelas[array_rand($kelas)],
                'kelompok'       => 'Kelompok ' . rand(1, 8),
                'tanggal_pakai'  => $tglDibuat->copy()->addDays(rand(1, 7)),
                'keterangan'     => 'Pengajuan untuk praktikum ' . $tglDibuat->format('M Y'),
                'status'         => $status,
                'approved_by'    => in_array($status, ['approved', 'completed', 'rejected']) ? $admin?->id : null,
                'approved_at'    => in_array($status, ['approved', 'completed']) ? $tglDibuat->copy()->addDays(1) : null,
                'completed_by'   => ($status === 'completed') ? $admin?->id : null,
                'completed_at'   => ($status === 'completed') ? $tglDibuat->copy()->addDays(2) : null,
                'reject_reason'  => ($status === 'rejected') ? 'Stok tidak mencukupi untuk semua item yang diminta.' : null,
                'created_at'     => $tglDibuat,
                'updated_at'     => $tglDibuat,
            ]);

            // Items
            if ($modul && $modul->items->isNotEmpty()) {
                foreach ($modul->items as $modulItem) {
                    $b = Bahan::find($modulItem->bahan_id);
                    if ($b) {
                        PengajuanItem::create([
                            'pengajuan_id'        => $pengajuan->id,
                            'bahan_id'            => $b->id,
                            'nama_bahan_snapshot' => $b->nama_bahan,
                            'satuan_snapshot'     => $b->satuan?->nama ?? 'pcs',
                            'jumlah'              => $modulItem->jumlah,
                        ]);
                    }
                }
            } else {
                $pilihanBahan = $bahans->random(min(rand(1, 3), $bahans->count()));
                foreach ($pilihanBahan as $b) {
                    PengajuanItem::create([
                        'pengajuan_id'        => $pengajuan->id,
                        'bahan_id'            => $b->id,
                        'nama_bahan_snapshot' => $b->nama_bahan,
                        'satuan_snapshot'     => $b->satuan?->nama ?? 'pcs',
                        'jumlah'              => rand(1, 10) * 5,
                    ]);
                }
            }

            // Kurangi stok untuk yang completed
            if ($status === 'completed') {
                $pengajuan->load('items');
                foreach ($pengajuan->items as $item) {
                    $b = Bahan::find($item->bahan_id);
                    if ($b && $b->stok >= $item->jumlah) {
                        $stokSebelum = $b->stok;
                        $b->decrement('stok', $item->jumlah);
                        $b->refresh();

                        LogStok::create([
                            'bahan_id'        => $b->id,
                            'tanggal'         => $pengajuan->completed_at ?? $tglDibuat,
                            'jenis'           => 'keluar',
                            'jumlah'          => $item->jumlah,
                            'stok_sebelum'    => $stokSebelum,
                            'stok_sesudah'    => $b->stok,
                            'reference_id'    => $pengajuan->id,
                            'reference_table' => 'pengajuan',
                            'keterangan'      => 'Pemakaian ' . $pengajuan->kode_pengajuan,
                            'created_by'      => $admin?->id,
                        ]);
                    }
                }
            }
        }

        // ──────────────────────────────────────────────────────────
        // 3. STOCK OPNAME — koreksi stok (3 record)
        // ──────────────────────────────────────────────────────────
        $jenisPenyesuaian = ['rusak', 'kadaluarsa', 'hilang', 'koreksi_lain'];
        $alasanList = [
            'rusak'        => 'Bahan rusak akibat tumpahan saat praktikum',
            'kadaluarsa'   => 'Bahan sudah melewati tanggal kadaluarsa',
            'hilang'       => 'Bahan tidak ditemukan setelah pengecekan fisik',
            'koreksi_lain' => 'Koreksi data setelah audit stok fisik bulanan',
        ];

        for ($i = 0; $i < 3; $i++) {
            $bahan       = $bahans->random();
            $jenis       = $jenisPenyesuaian[array_rand($jenisPenyesuaian)];
            $stokSebelum = $bahan->stok;
            $selisih     = rand(-15, -1);
            $stokSesuai  = max(0, $stokSebelum + $selisih);
            $tglOpname   = now()->subDays(rand(5, 30));

            $opname = StockOpname::create([
                'bahan_id'          => $bahan->id,
                'stok_sebelum'      => $stokSebelum,
                'stok_sesuai'       => $stokSesuai,
                'selisih'           => $stokSesuai - $stokSebelum,
                'alasan'            => $alasanList[$jenis],
                'jenis_penyesuaian' => $jenis,
                'created_by'        => $admin?->id,
                'created_at'        => $tglOpname,
            ]);

            $bahan->update(['stok' => $stokSesuai]);
            $bahan->refresh();

            LogStok::create([
                'bahan_id'        => $bahan->id,
                'tanggal'         => $tglOpname,
                'jenis'           => 'opname',
                'jumlah'          => abs($selisih),
                'stok_sebelum'    => $stokSebelum,
                'stok_sesudah'    => $stokSesuai,
                'reference_id'    => $opname->id,
                'reference_table' => 'stock_opname',
                'keterangan'      => 'Stock opname: ' . $jenis,
                'created_by'      => $admin?->id,
            ]);
        }

        $this->command->info('✓ DummyDataSeeder: 7 pengajuan, 5 bahan masuk, 3 stock opname berhasil dibuat.');
    }
}
