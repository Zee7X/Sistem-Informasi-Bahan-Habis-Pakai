<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bahan_masuk', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bahan_id');
            $table->integer('jumlah');
            $table->date('tanggal_masuk')->useCurrent();
            $table->string('pemasok', 200)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('bahan_id')
                ->references('id')
                ->on('bahan')
                ->onDelete('cascade');
            
            $table->index(['bahan_id', 'tanggal_masuk']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bahan_masuk');
    }
};
