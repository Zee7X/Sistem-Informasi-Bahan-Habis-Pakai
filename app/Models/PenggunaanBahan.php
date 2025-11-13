<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenggunaanBahan extends Model
{
    use HasFactory;

    protected $table = 'penggunaan_bahan';

    protected $fillable = [
        'tanggal_pemakaian',
        'waktu_input',
        'requester_user_id',
        'nama_pengisi',
        'nim_pengisi',
        'bahan_id',
        'nama_bahan_text',
        'jumlah',
        'satuan_id',
        'mata_kuliah',
        'kelas',
        'kelompok',
        'keterangan',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tanggal_pemakaian' => 'date',
        'waktu_input' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'satuan_id');
    }
}
