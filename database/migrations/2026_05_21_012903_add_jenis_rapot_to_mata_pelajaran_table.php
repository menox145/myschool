<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('mata_pelajaran', function (Blueprint $table) {
            $table->enum('jenis_rapot', ['dinniyyah', 'akademik', 'tahfidz'])
                ->default('akademik')
                ->after('nama_mapel');
            // $table->integer('kkm')->default(70)->after('urutan'); // HAPUS BARIS INI
        });
    }

    public function down(): void
    {
        Schema::table('mata_pelajaran', function (Blueprint $table) {
            $table->dropColumn('jenis_rapot');
            // $table->dropColumn('kkm'); // HAPUS JUGA
        });
    }
};
