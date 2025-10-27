<?php

namespace App\Services;

use App\Models\Satuan;
use Exception;

class SatuanService
{
    public function getAll($search = null, $perPage = 10)
    {
        return Satuan::when($search, function ($query, $search) {
            $query->where('nama', 'like', "%{$search}%");
        })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data)
    {
        return Satuan::create($data);
    }

    public function update($id, array $data)
    {
        $satuan = Satuan::findOrFail($id);
        $satuan->update($data);
        return $satuan;
    }

    public function delete($id)
    {
        $satuan = Satuan::findOrFail($id);

        if ($satuan->bahan()->exists()) {
            throw new Exception('Satuan tidak dapat dihapus karena masih digunakan pada data bahan.');
        }

        return $satuan->delete();
    }

    public function find($id)
    {
        return Satuan::findOrFail($id);
    }
}
