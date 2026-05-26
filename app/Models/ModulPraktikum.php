<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModulPraktikum extends Model
{
    use HasFactory;

    protected $table = 'modul_praktikum';

    protected $fillable = [
        'kode_modul',
        'nama_modul',
        'deskripsi',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ─── Scopes ───────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // ─── Relationships ───────────────────────────────────────────────

    public function items(): HasMany
    {
        return $this->hasMany(ModulPraktikumItem::class, 'modul_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pengajuanList(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'modul_id');
    }
}
