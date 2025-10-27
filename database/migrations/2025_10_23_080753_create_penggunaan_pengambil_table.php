<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penggunaan_pengambil', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('penggunaan_id');
            $table->string('nama_pengambil', 200);
            $table->string('nim_pengambil', 50)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('keterangan', 200)->nullable();
            $table->dateTime('created_at')->useCurrent();

            $table->foreign('penggunaan_id')
                ->references('id')->on('penggunaan_bahan')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('set null');

            $table->index('penggunaan_id', 'idx_pengambil_penggunaan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penggunaan_pengambil');
    }
};
