<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            $table->decimal('rph', 5, 2)->default(0)->after('guru_id');
            $table->decimal('pts', 5, 2)->default(0)->after('rph');
            $table->decimal('pas', 5, 2)->default(0)->after('pts');
            $table->decimal('hpa', 5, 2)->nullable()->after('pas'); // Hasil Penilaian Akhir
            $table->string('predikat', 2)->nullable()->after('hpa'); // A, B, C, D
        });
    }

    public function down(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            $table->dropColumn(['rph', 'pts', 'pas', 'hpa', 'predikat']);
        });
    }
};
