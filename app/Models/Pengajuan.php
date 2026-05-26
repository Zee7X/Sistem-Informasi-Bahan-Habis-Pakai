<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengajuan extends Model
{
    use HasFactory;

    protected $table = 'pengajuan';

    protected $fillable = [
        'kode_pengajuan',
        'user_id',
        'modul_id',
        'jenis',
        'mata_kuliah',
        'kelas',
        'kelompok',
        'jumlah_anggota',  // Jumlah anggota kelompok (tidak perlu tabel terpisah)
        'tanggal_pakai',
        'keterangan',
        'status',
        'reject_reason',
        'approved_by',
        'approved_at',
        'completed_by',
        'completed_at',
    ];

    protected $casts = [
        'tanggal_pakai' => 'date',
        'approved_at'   => 'datetime',
        'completed_at'  => 'datetime',
    ];

    // ─── Status helpers ─────────────────────────────────────────────

    public function isPendingReview(): bool
    {
        return $this->status === 'pending_review';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function canBeApproved(): bool
    {
        return $this->isPendingReview();
    }

    public function canBeRejected(): bool
    {
        return $this->isPendingReview();
    }

    /** Hanya pengajuan yang sudah approved yang bisa di-complete. */
    public function canBeCompleted(): bool
    {
        return $this->isApproved();
    }

    // ─── Relationships ───────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function modul(): BelongsTo
    {
        return $this->belongsTo(ModulPraktikum::class, 'modul_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PengajuanItem::class, 'pengajuan_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
