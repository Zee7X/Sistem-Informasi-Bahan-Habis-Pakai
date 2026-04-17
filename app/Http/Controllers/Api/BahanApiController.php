<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bahan;

class BahanApiController extends Controller
{
    public function search(Request $request)
    {
        $search = $request->query('q');
        $page = $request->query('page', 1);
        $perPage = 10;

        $query = Bahan::query()
            ->select('id', 'nama_bahan', 'kode_bahan', 'spesifikasi')
            ->when($search, function ($q) use ($search) {
                $q->where('nama_bahan', 'like', "%{$search}%")
                    ->orWhere('kode_bahan', 'like', "%{$search}%")
                    ->orWhere('spesifikasi', 'like', "%{$search}%");
            })
            ->orderBy('nama_bahan', 'asc');

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'items' => $paginator->items(),
            'hasMore' => $paginator->hasMorePages(),
        ]);
    }
}
