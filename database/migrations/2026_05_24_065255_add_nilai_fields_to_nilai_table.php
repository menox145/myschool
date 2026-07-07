<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            if (! Schema::hasColumn('nilai', 'rph')) {
                $table->decimal('rph', 5, 2)->default(0)->after('guru_id');
            }
            if (! Schema::hasColumn('nilai', 'pts')) {
                $table->decimal('pts', 5, 2)->default(0)->after('rph');
            }
            if (! Schema::hasColumn('nilai', 'pas')) {
                $table->decimal('pas', 5, 2)->default(0)->after('pts');
            }
            if (! Schema::hasColumn('nilai', 'hpa')) {
                $table->decimal('hpa', 5, 2)->nullable()->after('pas'); // Hasil Penilaian Akhir
            }
            if (! Schema::hasColumn('nilai', 'predikat')) {
                $table->string('predikat', 50)->nullable()->after('hpa'); // Predikat Arab/Latin
            }
        });
    }

    public function down(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            $columns = array_filter(['rph', 'pts', 'pas', 'hpa', 'predikat'], fn($column) => Schema::hasColumn('nilai', $column));
            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
