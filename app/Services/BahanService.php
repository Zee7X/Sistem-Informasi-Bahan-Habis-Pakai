<?php

namespace App\Services;

use App\Models\Bahan;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;

class BahanService
{
    public function list($search = null, $sort = 'latest', int $perPage = 10): LengthAwarePaginator
    {
        $query = Bahan::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_bahan', 'like', "%{$search}%")
                    ->orWhere('nama_bahan', 'like', "%{$search}%")
                    ->orWhere('spesifikasi', 'like', "%{$search}%");
            });
        }

        switch ($sort) {
            case 'name_asc':
                $query->orderBy('nama_bahan', 'asc');
                break;
            case 'stok_low':
                $query->orderBy('stok', 'asc');
                break;
            case 'stok_high':
                $query->orderBy('stok', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        return $query->paginate($perPage)->withQueryString();
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

    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return Bahan::orderBy('nama_bahan')->get();
    }
}
