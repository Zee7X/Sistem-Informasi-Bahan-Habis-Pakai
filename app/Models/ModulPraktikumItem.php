<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModulPraktikumItem extends Model
{
    protected $table = 'modul_praktikum_items';

    protected $fillable = [
        'modul_id',
        'bahan_id',
        'jumlah',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
    ];

    public function modul(): BelongsTo
    {
        return $this->belongsTo(ModulPraktikum::class, 'modul_id');
    }

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }
}
