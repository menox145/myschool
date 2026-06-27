<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahun_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->string('tahun', 9); // 2025/2026
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_selesai')->nullable();
            $table->boolean('aktif')->default(false);
            $table->timestamps();

            $table->unique(['tahun', 'semester']); // Biar ga dobel
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tahun_pelajaran');
    }
};
