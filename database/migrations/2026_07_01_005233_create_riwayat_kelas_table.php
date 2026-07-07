<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajaran')->onDelete('cascade');
            $table->enum('status', ['aktif', 'lulus', 'pindah', 'mengulang'])->default('aktif');
            $table->timestamps();

            $table->unique(['siswa_id', 'tahun_pelajaran_id']); // 1 siswa 1 kelas per tahun
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_kelas');
    }
};
