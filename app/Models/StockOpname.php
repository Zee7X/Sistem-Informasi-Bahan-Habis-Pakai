<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpname extends Model
{
    use HasFactory;

    protected $table = 'stock_opname';

    protected $fillable = [
        'bahan_id',
        'stok_sebelum',
        'stok_sesuai',
        'selisih',
        'alasan',
        'jenis_penyesuaian',
        'created_by',
    ];

    protected $casts = [
        'stok_sebelum' => 'integer',
        'stok_sesuai'  => 'integer',
        'selisih'      => 'integer',
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
