<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            $table->decimal('uh1', 5, 2)->nullable()->after('guru_id');
            $table->decimal('uh2', 5, 2)->nullable()->after('uh1');
            $table->decimal('uh3', 5, 2)->nullable()->after('uh2');
            $table->decimal('uh4', 5, 2)->nullable()->after('uh3');
            $table->decimal('uh5', 5, 2)->nullable()->after('uh4');
            $table->decimal('uh6', 5, 2)->nullable()->after('uh5');
            $table->decimal('rata_uh', 5, 2)->nullable()->after('uh6');
        });
    }

    public function down(): void
    {
        Schema::table('nilai', function (Blueprint $table) {
            $table->dropColumn(['uh1', 'uh2', 'uh3', 'uh4', 'uh5', 'uh6', 'rata_uh']);
        });
    }
};
