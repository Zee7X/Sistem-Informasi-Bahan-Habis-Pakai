<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah kolom jumlah_anggota ke tabel pengajuan.
 *
 * Alasan:
 *  - 1 pengajuan mewakili 1 kelompok praktikum.
 *  - Field 'kelompok' sudah ada untuk nama kelompok (ex: "Kelompok 3").
 *  - Field 'jumlah_anggota' menambah informasi berapa orang dalam kelompok
 *    tersebut — berguna untuk laporan konsumsi per-orang.
 *  - Tidak dibuat tabel terpisah karena kelompok bersifat ad-hoc
 *    (berubah tiap semester/mata kuliah).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->unsignedTinyInteger('jumlah_anggota')
                  ->nullable()
                  ->default(null)
                  ->after('kelompok')
                  ->comment('Jumlah anggota kelompok dalam pengajuan ini');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->dropColumn('jumlah_anggota');
        });
    }
};
