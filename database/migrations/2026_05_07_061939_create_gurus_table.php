<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('gurus'); // Hapus tabel lama kalo ada

        Schema::create('gurus', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 20)->unique();
            $table->string('nama', 100);
            $table->date('tgl_lahir')->nullable();
            $table->string('no_hp', 15)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('nik', 16)->unique()->nullable();
            $table->string('no_kk', 16)->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gurus');
    }
};
