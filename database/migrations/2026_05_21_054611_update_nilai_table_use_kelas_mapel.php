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
        Schema::table('nilai', function (Blueprint $table) {
            $table->foreignId('kelas_mapel_id')->after('siswa_id')->constrained('kelas_mapel')->cascadeOnDelete();
            $table->dropForeign(['kelas_id']);
            $table->dropForeign(['mapel_id']);
            $table->dropColumn(['kelas_id', 'mapel_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
