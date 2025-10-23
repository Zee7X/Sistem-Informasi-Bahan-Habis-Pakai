<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 150);
            $table->string('email', 150)->unique()->nullable(false);
            $table->string('password', 255)->nullable();
            $table->enum('role', ['admin', 'mahasiswa', 'ketua_jurusan'])->default('mahasiswa');
            $table->string('nim', 50)->nullable();
            $table->string('kelas', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
