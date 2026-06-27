<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->unique();
            $table->string('nama');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->date('tgl_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp')->nullable();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null');
            $table->string('foto')->nullable();

            // TAMBAHAN FIELD BARU
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->integer('penghasilan_ayah')->nullable();
            $table->integer('penghasilan_ibu')->nullable();
            $table->tinyInteger('anak_ke')->nullable();
            $table->year('tahun_masuk')->nullable();
            $table->enum('status', ['Aktif', 'Lulus', 'Pindah', 'Drop Out'])->default('Aktif');

            $table->foreignId('user_id')->constrained('users');
            $table->string('nama_penambah');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
