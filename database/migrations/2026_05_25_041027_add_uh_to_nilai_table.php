<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('kelas_mapel', function (Blueprint $table) {
            $table->tinyInteger('jumlah_uh')->default(3)->after('mapel_id'); // default 3 UH
        });
    }
    public function down(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            $table->dropColumn(['uh1', 'uh2', 'uh3', 'uh4', 'uh5', 'uh6', 'rata_uh']);
        });
    }
};
