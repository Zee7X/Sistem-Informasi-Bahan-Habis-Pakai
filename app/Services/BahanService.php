<?php

namespace App\Services;

use App\Models\Bahan;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;

class BahanService
{
    public function list($search = null, int $perPage = 10): LengthAwarePaginator
    {
        return Bahan::when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_bahan', 'like', "%{$search}%")
                    ->orWhere('nama_bahan', 'like', "%{$search}%")
                    ->orWhere('spesifikasi', 'like', "%{$search}%")
                    ->orWhere('stok', 'like', "%{$search}%")
                    ->orWhere('minimal_stok', 'like', "%{$search}%");
            });
        })
            ->orderBy('id', 'asc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): Bahan
    {
        return Bahan::create($data);
    }

    public function update(Bahan $bahan, array $data): Bahan
    {
        $bahan->update($data);
        return $bahan;
    }

    public function delete(Bahan $bahan): void
    {
        try {
            $bahan->delete();
        } catch (QueryException $e) {
            throw new \Exception('Bahan tidak dapat dihapus karena masih digunakan pada data lain.');
        }
    }
}
