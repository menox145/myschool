<?php

namespace Tests\Feature;

use App\Models\Absen;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\RiwayatKelas;
use App\Models\Siswa;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AbsenTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private TahunPelajaran $tapel;
    private Kelas $kelas;
    private Siswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $guru = Guru::create([
            'nip' => '1234567890',
            'nama' => 'Pak Wali',
        ]);
        $this->tapel = TahunPelajaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'aktif' => true,
        ]);
        $this->kelas = Kelas::create([
            'nama_kelas' => 'I A',
            'tingkat' => 1,
            'guru_id' => $guru->id,
            'jumlah_siswa' => 1,
            'tahun_pelajaran' => '2025/2026 - Ganjil',
            'user_id' => $this->admin->id,
            'nama_penambah' => $this->admin->name,
        ]);
        $this->siswa = Siswa::create([
            'nis' => '1001',
            'nisn' => '1234567890',
            'nama' => 'Budi',
            'jenis_kelamin' => 'L',
            'tgl_lahir' => '2010-01-01',
            'status' => 'Aktif',
            'user_id' => $this->admin->id,
            'nama_penambah' => $this->admin->name,
        ]);
        RiwayatKelas::create([
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tapel->id,
            'status' => 'aktif',
        ]);
    }

    public function test_admin_can_view_absen_page(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('absen.index', [
                'kelas_id' => $this->kelas->id,
                'tanggal' => '2026-07-07',
            ]));

        $response->assertOk();
        $response->assertSee('ABSEN Siswa');
        $response->assertSee('Budi');
    }

    public function test_admin_can_store_absen_siswa(): void
    {
        $response = $this->actingAs($this->admin)->post(route('absen.store'), [
            'kelas_id' => $this->kelas->id,
            'tanggal' => '2026-07-07',
            'absen' => [
                $this->siswa->id => [
                    'status' => 'Sakit',
                    'keterangan' => 'Demam',
                ],
            ],
        ]);

        $response->assertRedirect(route('absen.index', [
            'kelas_id' => $this->kelas->id,
            'tanggal' => '2026-07-07',
        ]));
        $this->assertDatabaseHas('absens', [
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tapel->id,
            'status' => 'Sakit',
            'keterangan' => 'Demam',
        ]);
    }

    public function test_admin_can_print_pdf_from_absen_page(): void
    {
        Absen::create([
            'siswa_id' => $this->siswa->id,
            'kelas_id' => $this->kelas->id,
            'tahun_pelajaran_id' => $this->tapel->id,
            'tanggal' => '2026-07-07',
            'status' => 'Hadir',
            'user_id' => $this->admin->id,
        ]);

        $this->actingAs($this->admin)
            ->get(route('absen.index', ['kelas_id' => $this->kelas->id]))
            ->assertOk()
            ->assertSee('Cetak Absen')
            ->assertSee(route('absen.cetak.print'));

        $this->actingAs($this->admin)
            ->get(route('absen.cetak.print', [
                'tahun_pelajaran_id' => $this->tapel->id,
                'kelas_id' => $this->kelas->id,
                'tanggal_mulai' => '2026-07-01',
                'tanggal_selesai' => '2026-07-31',
            ]))
            ->assertOk();
    }
}
