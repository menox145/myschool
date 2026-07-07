<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\RiwayatKelas;
use App\Models\Siswa;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_student_history_page(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $tahun = TahunPelajaran::create([
            'tahun' => '2024/2025',
            'semester' => 'Ganjil',
            'aktif' => 1,
        ]);
        $kelas = Kelas::create([
            'nama_kelas' => 'VI A',
            'tingkat' => 6,
            'jumlah_siswa' => 0,
            'tahun_pelajaran' => '2024/2025',
            'user_id' => $user->id,
        ]);
        $siswa = Siswa::create([
            'nis' => '1001',
            'nisn' => '1234567890',
            'nama' => 'Budi',
            'jenis_kelamin' => 'L',
            'tgl_lahir' => '2010-01-01',
            'status' => 'Aktif',
            'user_id' => $user->id,
            'nama_penambah' => $user->name,
        ]);
        RiwayatKelas::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $tahun->id,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($user)->get(route('siswa.riwayat.index'));

        $response->assertOk();
        $response->assertSee('Riwayat Perkembangan Siswa');
        $response->assertSee('Budi');
    }
}
