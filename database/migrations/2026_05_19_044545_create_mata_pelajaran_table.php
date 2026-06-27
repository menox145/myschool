<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mapel', 10)->unique(); // MTK, BIND, IPA
            $table->string('nama_mapel', 100);
            $table->integer('kkm')->default(75); // KKM default
            $table->enum('kelompok', ['A', 'B', 'C'])->default('A'); // A=Wajib, B=Mulok, C=Peminatan
            $table->integer('urutan')->default(0); // Buat urutin di rapot
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_pelajaran');
    }
};
