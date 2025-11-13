<?php

namespace App\Services;

use App\Models\Satuan;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;

class SatuanService
{
    public function list($search = null, int $perPage = 10): LengthAwarePaginator
    {
        return Satuan::when($search, function ($query, $search) {
            $query->where('nama', 'like', "%{$search}%");
        })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): Satuan
    {
        return Satuan::create($data);
    }

    public function update(Satuan $satuan, array $data): Satuan
    {
        $satuan->update($data);
        return $satuan;
    }

    public function delete(Satuan $satuan): void
    {
        try {
            if ($satuan->bahan()->exists()) {
                throw new \Exception('Satuan tidak dapat dihapus karena masih digunakan pada data bahan.');
            }
            $satuan->delete();
        } catch (QueryException $e) {
            throw new \Exception('Satuan tidak dapat dihapus karena masih digunakan pada data lain.');
        }
    }

    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return Satuan::orderBy('nama')->get();
    }
}
