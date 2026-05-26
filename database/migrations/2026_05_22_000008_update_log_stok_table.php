<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('log_stok', function (Blueprint $table) {
            $table->string('reference_table', 50)->nullable()->after('reference_id');
            $table->integer('stok_sebelum')->nullable()->after('jumlah');
        });

        // Update enum to add 'opname' value (MySQL specific)
        DB::statement("ALTER TABLE log_stok MODIFY COLUMN jenis ENUM('masuk','keluar','adjust','opname') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE log_stok MODIFY COLUMN jenis ENUM('masuk','keluar','adjust') NOT NULL");
        Schema::table('log_stok', function (Blueprint $table) {
            $table->dropColumn(['reference_table', 'stok_sebelum']);
        });
    }
};
