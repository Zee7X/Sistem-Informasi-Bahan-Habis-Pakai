<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bahan_masuk', function (Blueprint $table) {
            $table->string('no_faktur', 100)->nullable()->after('pemasok');
            $table->decimal('harga_satuan', 15, 2)->nullable()->after('no_faktur');
            $table->foreignId('approved_by_kjur')->nullable()->after('keterangan')
                  ->constrained('users')->nullOnDelete();
            $table->enum('status_kjur', ['pending', 'approved'])->default('approved')->after('approved_by_kjur');
            $table->foreignId('created_by')->nullable()->after('status_kjur')
                  ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bahan_masuk', function (Blueprint $table) {
            $table->dropForeign(['approved_by_kjur']);
            $table->dropForeign(['created_by']);
            $table->dropColumn(['no_faktur', 'harga_satuan', 'approved_by_kjur', 'status_kjur', 'created_by']);
        });
    }
};
