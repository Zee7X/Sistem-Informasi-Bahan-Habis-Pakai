<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_stok', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('barang_id')->constrained('barang')->onDelete('cascade');
            $table->dateTime('tanggal');
            $table->enum('jenis', ['masuk', 'keluar', 'adjust']);
            $table->integer('jumlah');
            $table->integer('stok_sesudah');
            $table->text('keterangan')->nullable();
            $table->bigInteger('reference_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->index(['barang_id', 'tanggal'], 'idx_log_barang');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_stok');
    }
};
