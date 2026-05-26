<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pengajuan', 30)->unique(); // BHP-2026-0001
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('modul_id')->nullable()->constrained('modul_praktikum')->nullOnDelete();
            $table->enum('jenis', ['modul', 'mandiri'])->default('modul');
            $table->string('mata_kuliah', 200)->nullable();
            $table->string('kelas', 100)->nullable();
            $table->string('kelompok', 100)->nullable();
            $table->date('tanggal_pakai');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['pending_review', 'approved', 'rejected', 'completed'])
                  ->default('pending_review');
            $table->text('reject_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('approved_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status'], 'idx_pengajuan_user_status');
            $table->index('tanggal_pakai', 'idx_pengajuan_tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan');
    }
};
