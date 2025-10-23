<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penggunaan_barang', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_pemakaian');
            $table->dateTime('waktu_input')->useCurrent();

            $table->unsignedBigInteger('requester_user_id')->nullable();
            $table->string('nama_pengisi', 200)->nullable();
            $table->string('nim_pengisi', 50)->nullable();

            $table->unsignedBigInteger('barang_id')->nullable();
            $table->string('nama_barang_text', 255)->nullable();

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

            $table->foreign('requester_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('barang_id')->references('id')->on('barang')->nullOnDelete();
            $table->foreign('satuan_id')->references('id')->on('satuan')->nullOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();

            $table->index('tanggal_pemakaian', 'idx_penggunaan_tanggal');
            $table->index('requester_user_id', 'idx_penggunaan_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penggunaan_barang');
    }
};
