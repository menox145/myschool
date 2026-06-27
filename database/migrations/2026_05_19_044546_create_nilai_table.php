<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('kelas_mapel_id')->constrained('kelas_mapel')->cascadeOnDelete();
            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajaran')->cascadeOnDelete();
            $table->foreignId('guru_id')->nullable()->constrained('gurus')->nullOnDelete();
            $table->integer('rph')->nullable()->default(0);
            $table->integer('pts')->nullable()->default(0);
            $table->integer('pas')->nullable()->default(0);
            $table->decimal('hpa', 5, 2)->nullable();
            $table->string('predikat', 5)->nullable();
            $table->timestamps();

            $table->unique(['siswa_id', 'kelas_mapel_id', 'tahun_pelajaran_id'], 'nilai_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai');
    }
};
