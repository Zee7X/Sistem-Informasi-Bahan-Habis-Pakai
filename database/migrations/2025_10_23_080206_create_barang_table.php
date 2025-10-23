<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id(); 
            $table->string('kode_barang', 100)->unique();
            $table->string('nama_barang', 200);
            $table->text('spesifikasi')->nullable();
            $table->integer('stok')->default(0);
            $table->unsignedBigInteger('satuan_id')->nullable();
            $table->integer('minimal_stok')->default(0);
            $table->string('lokasi', 200)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps(); 

            $table->foreign('satuan_id')
                ->references('id')
                ->on('satuan')
                ->onDelete('set null');
        });

        Schema::table('barang', function (Blueprint $table) {
            $table->index('kode_barang', 'idx_kode_barang');
            $table->index('nama_barang', 'idx_nama_barang');
        });
    }

    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropForeign(['satuan_id']);
            $table->dropIndex('idx_kode_barang');
            $table->dropIndex('idx_nama_barang');
        });

        Schema::dropIfExists('barang');
    }
};
