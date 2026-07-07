<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('kelas_mapel', 'jumlah_uh')) {
            return;
        }

        Schema::table('kelas_mapel', function (Blueprint $table) {
            $table->tinyInteger('jumlah_uh')->default(3)->after('mapel_id'); // default 3 UH
        });
    }
    public function down(): void
    {
        if (! Schema::hasColumn('kelas_mapel', 'jumlah_uh')) {
            return;
        }

        Schema::table('kelas_mapel', function (Blueprint $table) {
            $table->dropColumn(['jumlah_uh']);
        });
    }
};
