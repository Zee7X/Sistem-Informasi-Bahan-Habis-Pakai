<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bahan extends Model
{
    use HasFactory;

    protected $table = 'bahan';

    protected $fillable = [
        'kode_bahan',
        'nama_bahan',
        'spesifikasi',
        'stok',
        'satuan_id',
        'minimal_stok',
        'lokasi',
        'keterangan',
    ];

    protected $casts = [
        'stok'        => 'integer',
        'minimal_stok'=> 'integer',
    ];

    // ─── Accessories ────────────────────────────────────────────────

    /** Stok di bawah atau sama dengan batas minimal. */
    public function isKritis(): bool
    {
        return $this->stok <= $this->minimal_stok;
    }

    // ─── Relationships ───────────────────────────────────────────────

    public function satuan(): BelongsTo
    {
        return $this->belongsTo(Satuan::class, 'satuan_id');
    }

    public function bahanMasuk(): HasMany
    {
        return $this->hasMany(BahanMasuk::class, 'bahan_id');
    }

    public function stockOpname(): HasMany
    {
        return $this->hasMany(StockOpname::class, 'bahan_id');
    }

    public function logStok(): HasMany
    {
        return $this->hasMany(LogStok::class, 'bahan_id');
    }

    public function modulItems(): HasMany
    {
        return $this->hasMany(ModulPraktikumItem::class, 'bahan_id');
    }
}
