<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BahanMasuk extends Model
{
    use HasFactory;

    protected $table = 'bahan_masuk';

    protected $fillable = [
        'bahan_id',
        'jumlah',
        'tanggal_masuk',
        'pemasok',
        'no_faktur',
        'harga_satuan',
        'keterangan',
        'approved_by_kjur',
        'status_kjur',
        'created_by',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'harga_satuan'  => 'decimal:2',
    ];

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedByKjur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_kjur');
    }
}
