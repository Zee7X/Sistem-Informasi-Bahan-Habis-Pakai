<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PenggunaanBahan;


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

     public function penggunaan()
    {
        return $this->hasMany(PenggunaanBahan::class, 'bahan_id');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'satuan_id');
    }
}
