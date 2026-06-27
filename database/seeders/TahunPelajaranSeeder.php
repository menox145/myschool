<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TahunPelajaran; // TAMBAHIN INI

class TahunPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        TahunPelajaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'tgl_mulai' => '2025-07-15',
            'tgl_selesai' => '2025-12-20',
            'aktif' => true
        ]);

        TahunPelajaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Genap',
            'tgl_mulai' => '2026-01-06',
            'tgl_selesai' => '2026-06-15',
            'aktif' => false
        ]);
    }
}
