<?php

namespace App\Services;

use App\Models\Bahan;
use App\Models\LogStok;
use App\Models\Pengajuan;
use App\Models\PengajuanItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PengajuanService
{
    /**
     * Buat pengajuan baru beserta itemnya.
     * Stok TIDAK dipotong di sini — hanya saat complete().
     */
    public function store(array $data, User $user): Pengajuan
    {
        return DB::transaction(function () use ($data, $user) {
            $pengajuan = Pengajuan::create([
                'kode_pengajuan' => $this->generateKodePengajuan(),
                'user_id'        => $user->id,
                'modul_id'       => $data['modul_id'] ?? null,
                'jenis'          => $data['jenis'],
                'mata_kuliah'    => $data['mata_kuliah'] ?? null,
                'kelas'          => $data['kelas'] ?? null,
                'kelompok'       => $data['kelompok'] ?? null,
                'jumlah_anggota' => $data['jumlah_anggota'] ?? null,
                'tanggal_pakai'  => $data['tanggal_pakai'],
                'keterangan'     => $data['keterangan'] ?? null,
                'status'         => 'pending_review',
            ]);

            // Simpan tiap item beserta snapshot nama & satuan
            foreach ($data['items'] as $item) {
                $bahan = Bahan::findOrFail($item['bahan_id']);
                PengajuanItem::create([
                    'pengajuan_id'       => $pengajuan->id,
                    'bahan_id'           => $bahan->id,
                    'nama_bahan_snapshot'=> $bahan->nama_bahan,
                    'satuan_snapshot'    => $bahan->satuan?->nama ?? '-',
                    'jumlah'             => $item['jumlah'],
                ]);
            }

            return $pengajuan;
        });
    }

    /**
     * Admin menyetujui pengajuan → status APPROVED.
     * Stok belum dipotong di sini (soft state).
     */
    public function approve(Pengajuan $pengajuan, User $admin): void
    {
        if (! $pengajuan->canBeApproved()) {
            throw new \RuntimeException('Pengajuan tidak dapat disetujui dari status saat ini.');
        }

        $pengajuan->update([
            'status'      => 'approved',
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);
    }

    /**
     * Admin menolak pengajuan → status REJECTED (terminal).
     * Wajib menyertakan alasan penolakan.
     */
    public function reject(Pengajuan $pengajuan, User $admin, string $reason): void
    {
        if (! $pengajuan->canBeRejected()) {
            throw new \RuntimeException('Pengajuan tidak dapat ditolak dari status saat ini.');
        }

        $pengajuan->update([
            'status'        => 'rejected',
            'reject_reason' => $reason,
            'approved_by'   => $admin->id,
            'approved_at'   => now(),
        ]);
    }

    /**
     * Admin menandai pengajuan COMPLETED setelah bahan diserahkan.
     * CRITICAL: stok dipotong DI SINI dalam DB Transaction dengan lockForUpdate.
     */
    public function complete(Pengajuan $pengajuan, User $admin): void
    {
        if (! $pengajuan->canBeCompleted()) {
            throw new \RuntimeException('Pengajuan harus berstatus approved sebelum dapat di-complete.');
        }

        DB::transaction(function () use ($pengajuan, $admin) {
            // Load items dengan lock untuk mencegah race condition
            $items = $pengajuan->items()->with('bahan')->get();

            foreach ($items as $item) {
                // Lock baris bahan agar tidak ada transaksi paralel yang mengganggu
                $bahan = Bahan::lockForUpdate()->findOrFail($item->bahan_id);

                if ($bahan->stok < $item->jumlah) {
                    throw new \RuntimeException(
                        "Stok '{$bahan->nama_bahan}' tidak mencukupi. "
                        . "Tersedia: {$bahan->stok}, Dibutuhkan: {$item->jumlah}"
                    );
                }

                $stokSebelum = $bahan->stok;
                $bahan->decrement('stok', $item->jumlah);

                // Catat ke audit log stok
                LogStok::create([
                    'bahan_id'        => $bahan->id,
                    'tanggal'         => now(),
                    'jenis'           => 'keluar',
                    'jumlah'          => $item->jumlah,
                    'stok_sebelum'    => $stokSebelum,
                    'stok_sesudah'    => $bahan->stok,
                    'reference_table' => 'pengajuan',
                    'reference_id'    => $pengajuan->id,
                    'keterangan'      => "Pengambilan: {$pengajuan->kode_pengajuan}",
                    'created_by'      => $admin->id,
                ]);
            }

            $pengajuan->update([
                'status'       => 'completed',
                'completed_by' => $admin->id,
                'completed_at' => now(),
            ]);
        });
    }

    /**
     * Generate kode pengajuan unik format BHP-YYYY-XXXX.
     * Menggunakan DB lock untuk mencegah duplikasi pada akses konkuren.
     */
    public function generateKodePengajuan(): string
    {
        $year = now()->year;
        $prefix = "BHP-{$year}-";

        // Ambil nomor urut terbesar untuk tahun ini
        $last = Pengajuan::where('kode_pengajuan', 'like', "{$prefix}%")
            ->lockForUpdate()
            ->orderByDesc('id')
            ->value('kode_pengajuan');

        $nextNumber = $last
            ? (int) substr($last, -4) + 1
            : 1;

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
