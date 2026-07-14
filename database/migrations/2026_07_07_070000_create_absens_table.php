<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajaran')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('status', ['Hadir', 'Sakit', 'Izin', 'Alpa'])->default('Hadir');
            $table->string('keterangan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['siswa_id', 'tanggal', 'tahun_pelajaran_id']);
            $table->index(['kelas_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absens');
    }
};
