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
        $columns = [];

        if (Schema::hasColumn('nilai', 'kelas_id')) {
            $columns[] = 'kelas_id';
        }

        if (Schema::hasColumn('nilai', 'mapel_id')) {
            $columns[] = 'mapel_id';
        }

        if (empty($columns)) {
            return;
        }

        Schema::table('nilai', function (Blueprint $table) use ($columns) {
            if (in_array('kelas_id', $columns, true)) {
                $table->dropForeign(['kelas_id']);
            }

            if (in_array('mapel_id', $columns, true)) {
                $table->dropForeign(['mapel_id']);
            }

            $table->dropColumn($columns);
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
