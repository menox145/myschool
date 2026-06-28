<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mata_pelajaran', function (Blueprint $table) {
            // jenis_rapot udah ada, jadi skip aja
            // Kalo mau ubah enum jadi A,B,Mulok pake ini:
            // $table->enum('jenis_rapot', ['A', 'B', 'Mulok'])->default('A')->change();

            if (!Schema::hasColumn('mata_pelajaran', 'urutan')) {
                $table->tinyInteger('urutan')->default(1); // tanpa after() biar ga error
            }
        });
    }

    public function down(): void
    {
        Schema::table('mata_pelajaran', function (Blueprint $table) {
            $table->dropColumn('urutan');
        });
    }
};
