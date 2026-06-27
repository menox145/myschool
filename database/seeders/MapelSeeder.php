<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MataPelajaran;
use Illuminate\Support\Facades\DB;

class MapelLengkapSeeder extends Seeder
{
    public function run(): void
    {
        // Kosongin dulu biar ga dobel
        DB::table('mata_pelajaran')->truncate();

        $mapel = [
            // Kelompok A - Wajib
            ['kode_mapel' => 'PAI', 'nama_mapel' => 'Pendidikan Agama Islam', 'kkm' => 75, 'kelompok' => 'A', 'urutan' => 1],
            ['kode_mapel' => 'PKN', 'nama_mapel' => 'Pendidikan Pancasila', 'kkm' => 75, 'kelompok' => 'A', 'urutan' => 2],
            ['kode_mapel' => 'BIND', 'nama_mapel' => 'Bahasa Indonesia', 'kkm' => 75, 'kelompok' => 'A', 'urutan' => 3],
            ['kode_mapel' => 'MTK', 'nama_mapel' => 'Matematika', 'kkm' => 75, 'kelompok' => 'A', 'urutan' => 4],
            ['kode_mapel' => 'IPA', 'nama_mapel' => 'Ilmu Pengetahuan Alam', 'kkm' => 75, 'kelompok' => 'A', 'urutan' => 5],
            ['kode_mapel' => 'IPS', 'nama_mapel' => 'Ilmu Pengetahuan Sosial', 'kkm' => 75, 'kelompok' => 'A', 'urutan' => 6],
            ['kode_mapel' => 'BING', 'nama_mapel' => 'Bahasa Inggris', 'kkm' => 75, 'kelompok' => 'A', 'urutan' => 7],
            ['kode_mapel' => 'SBDP', 'nama_mapel' => 'Seni Budaya', 'kkm' => 75, 'kelompok' => 'A', 'urutan' => 8],
            ['kode_mapel' => 'PJOK', 'nama_mapel' => 'PJOK', 'kkm' => 75, 'kelompok' => 'A', 'urutan' => 9],
            ['kode_mapel' => 'PRAKARYA', 'nama_mapel' => 'Prakarya', 'kkm' => 75, 'kelompok' => 'A', 'urutan' => 10],

            // Kelompok B - Mulok
            ['kode_mapel' => 'BD', 'nama_mapel' => 'Bahasa Daerah', 'kkm' => 75, 'kelompok' => 'B', 'urutan' => 11],
            ['kode_mapel' => 'TIK', 'nama_mapel' => 'Informatika/TIK', 'kkm' => 75, 'kelompok' => 'B', 'urutan' => 12],
        ];

        foreach ($mapel as $m) {
            MataPelajaran::create($m);
        }
    }
}
