<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Hapus tabel sisa sistem lama yang sudah tidak digunakan.
 *
 * Sejarah:
 *  - penggunaan_bahan & penggunaan_pengambil adalah tabel dari iterasi
 *    pertama sistem sebelum arsitektur diubah ke pengajuan + pengajuan_items.
 *  - Tidak ada controller, route, maupun halaman UI yang masih mengakses
 *    kedua tabel ini.
 *  - Fungsionalitasnya sudah sepenuhnya digantikan oleh:
 *      • pengajuan          (header transaksi)
 *      • pengajuan_items    (detail item + snapshot nama/satuan)
 *      • log_stok           (audit trail pergerakan stok)
 */
return new class extends Migration
{
    public function up(): void
    {
        // Hapus child table dulu (ada FK ke penggunaan_bahan)
        Schema::dropIfExists('penggunaan_pengambil');
        Schema::dropIfExists('penggunaan_bahan');
    }

    public function down(): void
    {
        // Restore penggunaan_bahan
        Schema::create('penggunaan_bahan', function ($table) {
            $table->id();
            $table->date('tanggal_pemakaian');
            $table->dateTime('waktu_input')->useCurrent();
            $table->unsignedBigInteger('requester_user_id')->nullable();
            $table->string('nama_pengisi', 200)->nullable();
            $table->string('nim_pengisi', 50)->nullable();
            $table->unsignedBigInteger('bahan_id')->nullable();
            $table->string('nama_bahan_text', 255)->nullable();
            $table->integer('jumlah')->default(1);
            $table->unsignedBigInteger('satuan_id')->nullable();
            $table->string('mata_kuliah', 200)->nullable();
            $table->string('kelas', 100)->nullable();
            $table->string('kelompok', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();
        });

        // Restore penggunaan_pengambil
        Schema::create('penggunaan_pengambil', function ($table) {
            $table->id();
            $table->unsignedBigInteger('penggunaan_id');
            $table->string('nama_pengambil', 200);
            $table->string('nim_pengambil', 50)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('keterangan', 200)->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->foreign('penggunaan_id')->references('id')->on('penggunaan_bahan')->onDelete('cascade');
        });
    }
};
