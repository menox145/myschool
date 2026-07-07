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
        if (Schema::hasColumn('mata_pelajaran', 'jenis_rapot')) {
            return;
        }

        Schema::table('mata_pelajaran', function (Blueprint $table) {
            $table->enum('jenis_rapot', ['akademik', 'dinniyyah', 'tahfidz'])
                ->default('akademik')
                ->after('kelompok');
        });
    }
    public function down(): void
    {
        if (! Schema::hasColumn('mata_pelajaran', 'jenis_rapot')) {
            return;
        }

        Schema::table('mata_pelajaran', function (Blueprint $table) {
            $table->dropColumn('jenis_rapot');
        });
    }
};
