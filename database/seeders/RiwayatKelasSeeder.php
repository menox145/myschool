<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\RiwayatKelas;
use App\Models\TahunPelajaran;
use Illuminate\Support\Facades\Schema;

class RiwayatKelasSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cek ada tahun aktif ga
        $tahunAktif = TahunPelajaran::where('aktif', 1)->first();

        if (!$tahunAktif) {
            $this->command->error('Ga ada Tahun Pelajaran yang aktif! Aktifin dulu di menu Tahun Pelajaran.');
            return;
        }

        // 2. Cek kolom kelas_id masih ada ga di tabel siswas
        if (!Schema::hasColumn('siswas', 'kelas_id')) {
            $this->command->info('Kolom kelas_id udah ga ada di tabel siswas. Seeder diskip.');
            return;
        }

        // 3. Ambil semua siswa yg punya kelas_id
        $siswas = Siswa::whereNotNull('kelas_id')->get();
        $count = 0;

        foreach ($siswas as $siswa) {
            // Cek udah ada riwayat belum biar ga double
            $exist = RiwayatKelas::where('siswa_id', $siswa->id)
                ->where('tahun_pelajaran_id', $tahunAktif->id)
                ->exists();

            if (!$exist) {
                RiwayatKelas::create([
                    'siswa_id' => $siswa->id,
                    'kelas_id' => $siswa->kelas_id,
                    'tahun_pelajaran_id' => $tahunAktif->id,
                    'status' => 'aktif',
                ]);
                $count++;
            }
        }

        $this->command->info("Berhasil migrasi {$count} data siswa ke riwayat_kelas untuk tahun {$tahunAktif->tahun} - {$tahunAktif->semester}");
    }
}
