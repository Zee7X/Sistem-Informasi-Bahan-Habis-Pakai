<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuan')->cascadeOnDelete();
            $table->foreignId('bahan_id')->nullable()->constrained('bahan')->nullOnDelete();
            // Snapshot: simpan nama & satuan saat pengajuan agar audit trail tidak berubah
            $table->string('nama_bahan_snapshot', 255);
            $table->string('satuan_snapshot', 50);
            $table->decimal('jumlah', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_items');
    }
};
