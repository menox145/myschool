<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('kelas', 'tingkat')) {
            return;
        }

        Schema::table('kelas', function (Blueprint $table) {
            $table->tinyInteger('tingkat')->default(1)->after('id')->comment('1=kelas1, 2=kelas2, dst');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('kelas', 'tingkat')) {
            return;
        }

        Schema::table('kelas', function (Blueprint $table) {
            $table->dropColumn('tingkat');
        });
    }
};
