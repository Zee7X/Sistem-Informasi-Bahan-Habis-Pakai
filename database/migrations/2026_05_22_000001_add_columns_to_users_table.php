<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('program_studi', 100)->nullable()->after('kelas');
            $table->string('angkatan', 10)->nullable()->after('program_studi');
            $table->string('no_telp', 20)->nullable()->after('angkatan');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['program_studi', 'angkatan', 'no_telp']);
        });
    }
};
