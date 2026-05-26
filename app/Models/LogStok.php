<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogStok extends Model
{
    protected $table = 'log_stok';

    // Tidak ada updated_at di tabel ini
    const UPDATED_AT = null;

    protected $fillable = [
        'bahan_id',
        'tanggal',
        'jenis',
        'jumlah',
        'stok_sebelum',
        'stok_sesudah',
        'reference_table',
        'reference_id',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'tanggal'    => 'datetime',
        'created_at' => 'datetime',
    ];

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
