<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nilai_harian', function (Blueprint $table) {
            $table->string('nama_user')->nullable()->after('user_id');
            $table->string('nama_mapel')->nullable()->after('kelas_mapel_id');
        });
    }

    public function down(): void
    {
        Schema::table('nilai_harian', function (Blueprint $table) {
            $table->dropColumn(['nama_user', 'nama_mapel']);
        });
    }
};
