<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nilai_harian', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->after('tahun_pelajaran_id');
            // kelas_mapel_id biar gampang dapet nama mapel tanpa join panjang
            $table->foreignId('kelas_mapel_id')->nullable()->constrained('kelas_mapel')->onDelete('set null')->after('sub_bab_mapel_id');
        });
    }

    public function down(): void
    {
        Schema::table('nilai_harian', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->dropForeign(['kelas_mapel_id']);
            $table->dropColumn('kelas_mapel_id');
        });
    }
};
