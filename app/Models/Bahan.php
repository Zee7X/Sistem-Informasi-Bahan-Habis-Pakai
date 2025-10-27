<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


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
        'keterangan'
    ];
}
