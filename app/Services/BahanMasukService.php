<?php

namespace App\Services;

use App\Models\Bahan;
use App\Models\BahanMasuk;
use App\Models\LogStok;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class BahanMasukService
{
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $search    = $filters['search'] ?? null;
        $startDate = $filters['start_date'] ?? null;
        $endDate   = $filters['end_date'] ?? null;

        return BahanMasuk::with('bahan.satuan', 'createdBy')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('bahan', function ($q2) use ($search) {
                        $q2->where('nama_bahan', 'like', "%{$search}%")
                           ->orWhere('kode_bahan', 'like', "%{$search}%");
                    })->orWhere('pemasok', 'like', "%{$search}%")
                      ->orWhere('no_faktur', 'like', "%{$search}%");
                });
            })
            ->when($startDate, fn ($q, $d) => $q->whereDate('tanggal_masuk', '>=', $d))
            ->when($endDate, fn ($q, $d) => $q->whereDate('tanggal_masuk', '<=', $d))
            ->orderByDesc('tanggal_masuk')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data, ?User $user = null): BahanMasuk
    {
        return DB::transaction(function () use ($data, $user) {
            $data['created_by'] = $user?->id;

            $masuk = BahanMasuk::create($data);

            $bahan = Bahan::lockForUpdate()->findOrFail($data['bahan_id']);
            $stokSebelum = $bahan->stok;
            $bahan->stok += $data['jumlah'];
            $bahan->save();

            // Catat ke audit log
            LogStok::create([
                'bahan_id'        => $bahan->id,
                'tanggal'         => now(),
                'jenis'           => 'masuk',
                'jumlah'          => $data['jumlah'],
                'stok_sebelum'    => $stokSebelum,
                'stok_sesudah'    => $bahan->stok,
                'reference_table' => 'bahan_masuk',
                'reference_id'    => $masuk->id,
                'keterangan'      => 'Stok masuk dari: ' . ($data['pemasok'] ?? '-'),
                'created_by'      => $user?->id,
            ]);

            return $masuk;
        });
    }

    public function delete(BahanMasuk $masuk): void
    {
        DB::transaction(function () use ($masuk) {
            $bahan = Bahan::lockForUpdate()->findOrFail($masuk->bahan_id);
            // Kembalikan stok (reverse entry)
            $bahan->stok -= $masuk->jumlah;
            if ($bahan->stok < 0) {
                throw new \RuntimeException('Tidak dapat menghapus karena stok akan menjadi negatif.');
            }
            $bahan->save();
            $masuk->delete();
        });
    }
}
