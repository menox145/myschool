<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nilai_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('sub_bab_mapel_id')->constrained('sub_bab_mapel')->onDelete('cascade');
            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajaran')->onDelete('cascade');
            $table->decimal('nilai', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['siswa_id', 'sub_bab_mapel_id', 'tahun_pelajaran_id'], 'nilai_harian_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_harian');
    }
};
