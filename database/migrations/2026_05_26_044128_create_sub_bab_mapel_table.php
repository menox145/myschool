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
        Schema::create('sub_bab_mapel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bab_mapel_id')->constrained('bab_mapel')->onDelete('cascade');
            $table->string('nama_sub_bab'); // tulis abja, tarjim 1, hal 3
            $table->integer('urutan')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_bab_mapel');
    }
};
