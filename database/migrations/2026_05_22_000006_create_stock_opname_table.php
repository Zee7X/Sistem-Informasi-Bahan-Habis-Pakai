<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opname', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_id')->constrained('bahan')->cascadeOnDelete();
            $table->integer('stok_sebelum');
            $table->integer('stok_sesuai');
            // selisih = stok_sesuai - stok_sebelum (negatif = pengurangan)
            $table->integer('selisih')->default(0);
            $table->text('alasan'); // WAJIB DIISI
            $table->enum('jenis_penyesuaian', ['rusak', 'kadaluarsa', 'hilang', 'koreksi_lain']);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname');
    }
};
