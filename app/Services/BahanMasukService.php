<?php

namespace App\Services;

use App\Models\Bahan;
use App\Models\BahanMasuk;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class BahanMasukService
{
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $search = $filters['search'] ?? null;
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;

        return BahanMasuk::with('bahan.satuan')
            ->when($search, function ($query, $search) {
                $query->where(function ($q1) use ($search) {
                    $q1->whereHas('bahan', function ($q) use ($search) {
                        $q->where('nama_bahan', 'like', "%{$search}%")
                          ->orWhere('kode_bahan', 'like', "%{$search}%")
                          ->orWhere('spesifikasi', 'like', "%{$search}%");
                    })->orWhere('pemasok', 'like', "%{$search}%");
                });
            })
            ->when($startDate, function ($query, $date) {
                $query->whereDate('tanggal_masuk', '>=', $date);
            })
            ->when($endDate, function ($query, $date) {
                $query->whereDate('tanggal_masuk', '<=', $date);
            })
            ->orderBy('tanggal_masuk', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): BahanMasuk
    {
        return DB::transaction(function () use ($data) {
            $masuk = BahanMasuk::create($data);
            
            // Update stock in Master Bahan
            $bahan = Bahan::lockForUpdate()->find($data['bahan_id']);
            $bahan->stok += $data['jumlah'];
            $bahan->save();

            return $masuk;
        });
    }

    public function delete(BahanMasuk $masuk): void
    {
        DB::transaction(function () use ($masuk) {
            // Revert stock in Master Bahan
            $bahan = Bahan::lockForUpdate()->find($masuk->bahan_id);
            $bahan->stok -= $masuk->jumlah;
            $bahan->save();

            $masuk->delete();
        });
    }
}
