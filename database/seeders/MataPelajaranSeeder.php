<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MataPelajaran;

class MataPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Dinniyyah
            ['kode_mapel' => 'QUR', 'nama_mapel' => "Al-Qur'an Hadits", 'jenis_rapot' => 'dinniyyah', 'urutan' => 1, 'kkm' => 75],
            ['kode_mapel' => 'AQD', 'nama_mapel' => 'Aqidah Akhlak', 'jenis_rapot' => 'dinniyyah', 'urutan' => 2, 'kkm' => 75],
            ['kode_mapel' => 'FIQ', 'nama_mapel' => 'Fiqih', 'jenis_rapot' => 'dinniyyah', 'urutan' => 3, 'kkm' => 75],
            ['kode_mapel' => 'SKI', 'nama_mapel' => 'SKI', 'jenis_rapot' => 'dinniyyah', 'urutan' => 4, 'kkm' => 75],
            ['kode_mapel' => 'B.AR', 'nama_mapel' => 'Bahasa Arab', 'jenis_rapot' => 'dinniyyah', 'urutan' => 5, 'kkm' => 75],

            // Tahfidz
            ['kode_mapel' => 'TAH', 'nama_mapel' => 'Tahfidz', 'jenis_rapot' => 'tahfidz', 'urutan' => 6, 'kkm' => 80],

            // Akademik
            ['kode_mapel' => 'PAI', 'nama_mapel' => 'Pendidikan Agama Islam', 'jenis_rapot' => 'akademik', 'urutan' => 7, 'kkm' => 70],
            ['kode_mapel' => 'PKN', 'nama_mapel' => 'PKN', 'jenis_rapot' => 'akademik', 'urutan' => 8, 'kkm' => 70],
            ['kode_mapel' => 'BIN', 'nama_mapel' => 'Bahasa Indonesia', 'jenis_rapot' => 'akademik', 'urutan' => 9, 'kkm' => 70],
            ['kode_mapel' => 'MTK', 'nama_mapel' => 'Matematika', 'jenis_rapot' => 'akademik', 'urutan' => 10, 'kkm' => 70],
            ['kode_mapel' => 'IPA', 'nama_mapel' => 'IPA', 'jenis_rapot' => 'akademik', 'urutan' => 11, 'kkm' => 70],
            ['kode_mapel' => 'IPS', 'nama_mapel' => 'IPS', 'jenis_rapot' => 'akademik', 'urutan' => 12, 'kkm' => 70],
            ['kode_mapel' => 'BIG', 'nama_mapel' => 'Bahasa Inggris', 'jenis_rapot' => 'akademik', 'urutan' => 13, 'kkm' => 70],
        ];

        foreach ($data as $item) {
            MataPelajaran::updateOrCreate(['kode_mapel' => $item['kode_mapel']], $item);
        }
    }
}
