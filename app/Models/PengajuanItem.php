<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanItem extends Model
{
    protected $table = 'pengajuan_items';

    protected $fillable = [
        'pengajuan_id',
        'bahan_id',
        'nama_bahan_snapshot',
        'satuan_snapshot',
        'jumlah',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }
}
