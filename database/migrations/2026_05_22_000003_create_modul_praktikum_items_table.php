<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modul_praktikum_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modul_id')->constrained('modul_praktikum')->cascadeOnDelete();
            $table->foreignId('bahan_id')->constrained('bahan')->cascadeOnDelete();
            $table->decimal('jumlah', 10, 2);
            $table->timestamps();
            $table->unique(['modul_id', 'bahan_id'], 'uq_modul_bahan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modul_praktikum_items');
    }
};
