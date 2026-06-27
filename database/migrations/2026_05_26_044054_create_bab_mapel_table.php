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
        Schema::create('bab_mapel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_mapel_id')->constrained('kelas_mapel')->onDelete('cascade');
            $table->string('nama_bab'); // Bab 1, Bab 2
            $table->integer('urutan')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bab_mapel');
    }
};
