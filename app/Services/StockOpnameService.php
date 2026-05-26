<?php

namespace App\Services;

use App\Models\Bahan;
use App\Models\LogStok;
use App\Models\StockOpname;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StockOpnameService
{
    /**
     * Lakukan penyesuaian stok manual (broken, expired, etc.).
     * Selisih negatif = pengurangan, positif = koreksi naik.
     */
    public function adjust(array $data, User $user): StockOpname
    {
        return DB::transaction(function () use ($data, $user) {
            $bahan = Bahan::lockForUpdate()->findOrFail($data['bahan_id']);

            $stokSebelum = $bahan->stok;
            $stokSesuai  = (int) $data['stok_sesuai'];
            $selisih     = $stokSesuai - $stokSebelum;

            // Update stok aktual
            $bahan->stok = $stokSesuai;
            $bahan->save();

            // Simpan record opname
            $opname = StockOpname::create([
                'bahan_id'          => $bahan->id,
                'stok_sebelum'      => $stokSebelum,
                'stok_sesuai'       => $stokSesuai,
                'selisih'           => $selisih,
                'alasan'            => $data['alasan'],
                'jenis_penyesuaian' => $data['jenis_penyesuaian'],
                'created_by'        => $user->id,
            ]);

            // Catat ke log stok untuk audit trail
            LogStok::create([
                'bahan_id'        => $bahan->id,
                'tanggal'         => now(),
                'jenis'           => 'opname',
                'jumlah'          => abs($selisih),
                'stok_sebelum'    => $stokSebelum,
                'stok_sesudah'    => $stokSesuai,
                'reference_table' => 'stock_opname',
                'reference_id'    => $opname->id,
                'keterangan'      => "Opname [{$data['jenis_penyesuaian']}]: {$data['alasan']}",
                'created_by'      => $user->id,
            ]);

            return $opname;
        });
    }
}
