<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modul_praktikum', function (Blueprint $table) {
            $table->id();
            $table->string('kode_modul', 50)->unique();
            $table->string('nama_modul', 200);
            $table->text('deskripsi')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modul_praktikum');
    }
};
