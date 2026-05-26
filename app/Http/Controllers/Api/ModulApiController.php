<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ModulPraktikum;
use Illuminate\Http\JsonResponse;

class ModulApiController extends Controller
{
    /** Return items dari sebuah modul untuk auto-populate form pengajuan mahasiswa. */
    public function items(ModulPraktikum $modul): JsonResponse
    {
        $items = $modul->items()
            ->with('bahan.satuan')
            ->get()
            ->map(fn ($item) => [
                'bahan_id'   => $item->bahan_id,
                'nama_bahan' => $item->bahan->nama_bahan,
                'satuan'     => $item->bahan->satuan?->nama ?? '-',
                'stok'       => $item->bahan->stok,
                'jumlah'     => (float) $item->jumlah,
            ]);

        return response()->json(['items' => $items]);
    }
}
