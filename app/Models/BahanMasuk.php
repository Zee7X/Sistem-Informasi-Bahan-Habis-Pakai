<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanMasuk extends Model
{
    use HasFactory;

    protected $table = 'bahan_masuk';

    protected $fillable = [
        'bahan_id',
        'jumlah',
        'tanggal_masuk',
        'pemasok',
        'keterangan'
    ];

    protected $casts = [
        'tanggal_masuk' => 'date'
    ];

    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }
}
