<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\GuruPiket;
use App\Models\Kelas;
use App\Models\KelasMapel;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\RiwayatKelas;
use App\Models\Siswa;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuUpdateTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_kelas_creation_does_not_require_jumlah_siswa(): void
    {
        $guru = Guru::create([
            'nip' => '9999999999',
            'nama' => 'Guru Baru',
        ]);
        TahunPelajaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'aktif' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('kelas.store'), [
            'nama_kelas' => 'X A',
            'tingkat' => 1,
            'guru_id' => $guru->id,
            'tahun_pelajaran' => '2026/2027 - Ganjil',
        ]);

        $response->assertRedirect(route('kelas'));
        $this->assertDatabaseHas('kelas', [
            'nama_kelas' => 'X A',
            'guru_id' => $guru->id,
        ]);
    }

    public function test_dashboard_shows_real_time_statistics_and_piket_schedule(): void
    {
        $guruPiket = Guru::create(['nip' => '1111111111', 'nama' => 'Guru Piket 1']);
        Guru::create(['nip' => '2222222222', 'nama' => 'Guru Piket 2']);
        Kelas::create([
            'nama_kelas' => 'IX A',
            'tingkat' => 9,
            'guru_id' => 1,
            'jumlah_siswa' => 0,
            'tahun_pelajaran' => '2026/2027 - Ganjil',
            'user_id' => $this->admin->id,
            'nama_penambah' => $this->admin->name,
        ]);
        MataPelajaran::create([
            'kode_mapel' => 'PJK',
            'nama_mapel' => 'Pendidikan Jasmani',
            'kkm' => 75,
            'kelompok' => 'A',
            'jenis_rapot' => 'akademik',
        ]);
        Siswa::create([
            'nis' => '9001',
            'nisn' => '9000000001',
            'nama' => 'Siswa Test',
            'jenis_kelamin' => 'L',
            'tgl_lahir' => '2010-01-01',
            'status' => 'Aktif',
            'user_id' => $this->admin->id,
            'nama_penambah' => $this->admin->name,
        ]);
        GuruPiket::create([
            'hari' => 'Senin',
            'guru_id' => $guruPiket->id,
            'urutan' => 1,
        ]);

        $response = $this->actingAs($this->admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Jadwal Guru Piket');
        $response->assertSee('Senin');
        $response->assertSee('Total Siswa');
        $response->assertSee((string) Siswa::count());
        $response->assertSee('Guru Piket 1');
    }

    public function test_admin_can_update_siswa(): void
    {
        $siswa = Siswa::create([
            'nis' => '1001',
            'nisn' => '1234567890',
            'nama' => 'Budi',
            'jenis_kelamin' => 'L',
            'tgl_lahir' => '2010-01-01',
            'status' => 'Aktif',
            'user_id' => $this->admin->id,
            'nama_penambah' => $this->admin->name,
        ]);

        $response = $this->actingAs($this->admin)->put(route('siswa.update', $siswa), [
            'nis' => '1001',
            'nisn' => '1234567890',
            'nama' => 'Budi Update',
            'jenis_kelamin' => 'L',
            'tgl_lahir' => '2010-01-01',
            'alamat' => 'Alamat baru',
            'no_hp' => '08123456789',
            'status' => 'Aktif',
        ]);

        $response->assertRedirect(route('siswa'));
        $this->assertDatabaseHas('siswas', [
            'id' => $siswa->id,
            'nama' => 'Budi Update',
            'alamat' => 'Alamat baru',
        ]);
    }

    public function test_admin_can_update_guru(): void
    {
        $guru = Guru::create([
            'nip' => '1234567890',
            'nama' => 'Pak Ahmad',
        ]);

        $response = $this->actingAs($this->admin)->put(route('guru.update', $guru->id), [
            'nip' => '1234567890',
            'nama' => 'Pak Ahmad Update',
            'tgl_lahir' => '1980-01-01',
            'no_hp' => '08111111111',
            'email' => 'ahmad@example.test',
            'nik' => '1234567890123456',
            'no_kk' => '6543210987654321',
        ]);

        $response->assertRedirect(route('guru'));
        $this->assertDatabaseHas('gurus', [
            'id' => $guru->id,
            'nama' => 'Pak Ahmad Update',
            'email' => 'ahmad@example.test',
        ]);
    }

    public function test_admin_can_update_kelas(): void
    {
        $guru = Guru::create([
            'nip' => '1234567890',
            'nama' => 'Pak Wali',
        ]);
        TahunPelajaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'aktif' => true,
        ]);
        $kelas = Kelas::create([
            'nama_kelas' => 'I A',
            'tingkat' => 1,
            'guru_id' => $guru->id,
            'jumlah_siswa' => 10,
            'tahun_pelajaran' => '2025/2026 - Ganjil',
            'user_id' => $this->admin->id,
            'nama_penambah' => $this->admin->name,
        ]);

        $response = $this->actingAs($this->admin)->put(route('kelas.update', $kelas), [
            'nama_kelas' => 'II A',
            'tingkat' => 2,
            'guru_id' => $guru->id,
            'tahun_pelajaran' => '2025/2026 - Ganjil',
        ]);

        $response->assertRedirect(route('kelas'));
        $this->assertDatabaseHas('kelas', [
            'id' => $kelas->id,
            'nama_kelas' => 'II A',
            'tingkat' => 2,
        ]);
    }

    public function test_admin_can_update_mapel(): void
    {
        $mapel = MataPelajaran::create([
            'kode_mapel' => 'MTK',
            'nama_mapel' => 'Matematika',
            'kkm' => 75,
            'kelompok' => 'A',
            'jenis_rapot' => 'akademik',
        ]);

        $response = $this->actingAs($this->admin)->put(route('mapel.update', $mapel), [
            'kode_mapel' => 'MTK',
            'nama_mapel' => 'Matematika Update',
            'kkm' => 80,
            'kelompok' => 'A',
            'jenis_rapot' => 'akademik',
        ]);

        $response->assertRedirect(route('mapel.index'));
        $this->assertDatabaseHas('mata_pelajaran', [
            'id' => $mapel->id,
            'nama_mapel' => 'Matematika Update',
            'kkm' => 80,
        ]);
    }

    public function test_admin_can_update_tahun_pelajaran(): void
    {
        $tapel = TahunPelajaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'aktif' => false,
        ]);

        $response = $this->actingAs($this->admin)->put(route('tahun-pelajaran.update', $tapel), [
            'tahun' => '2025/2026',
            'semester' => 'Genap',
            'tgl_mulai' => '2026-01-01',
            'tgl_selesai' => '2026-06-30',
            'aktif' => 1,
        ]);

        $response->assertRedirect(route('tahun-pelajaran.index'));
        $this->assertDatabaseHas('tahun_pelajaran', [
            'id' => $tapel->id,
            'semester' => 'Genap',
            'aktif' => 1,
        ]);
    }

    public function test_admin_can_update_kelas_mapel(): void
    {
        $guru = Guru::create([
            'nip' => '1234567890',
            'nama' => 'Pak Mapel',
        ]);
        $tapel = TahunPelajaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'aktif' => true,
        ]);
        $kelas = Kelas::create([
            'nama_kelas' => 'I A',
            'tingkat' => 1,
            'guru_id' => $guru->id,
            'jumlah_siswa' => 10,
            'tahun_pelajaran' => '2025/2026 - Ganjil',
            'user_id' => $this->admin->id,
            'nama_penambah' => $this->admin->name,
        ]);
        $mapel = MataPelajaran::create([
            'kode_mapel' => 'BIN',
            'nama_mapel' => 'Bahasa Indonesia',
            'kkm' => 75,
            'kelompok' => 'A',
            'jenis_rapot' => 'akademik',
        ]);
        $kelasMapel = KelasMapel::create([
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'guru_id' => $guru->id,
            'tahun_pelajaran_id' => $tapel->id,
            'jam_pelajaran' => 2,
        ]);

        $response = $this->actingAs($this->admin)->put(route('kelas-mapel.update', $kelasMapel), [
            'guru_id' => $guru->id,
            'jam_pelajaran' => 4,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('kelas_mapel', [
            'id' => $kelasMapel->id,
            'jam_pelajaran' => 4,
        ]);
    }

    public function test_admin_can_store_nilai_with_kelas_mapel_guru(): void
    {
        Guru::create([
            'nip' => '1111111111',
            'nama' => 'Guru Lain',
        ]);
        $guruMapel = Guru::create([
            'nip' => '2222222222',
            'nama' => 'Guru Mapel',
        ]);
        $tapel = TahunPelajaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'aktif' => true,
        ]);
        $kelas = Kelas::create([
            'nama_kelas' => 'I A',
            'tingkat' => 1,
            'guru_id' => $guruMapel->id,
            'jumlah_siswa' => 10,
            'tahun_pelajaran' => '2025/2026 - Ganjil',
            'user_id' => $this->admin->id,
            'nama_penambah' => $this->admin->name,
        ]);
        $mapel = MataPelajaran::create([
            'kode_mapel' => 'IPA',
            'nama_mapel' => 'IPA',
            'kkm' => 75,
            'kelompok' => 'A',
            'jenis_rapot' => 'akademik',
        ]);
        $kelasMapel = KelasMapel::create([
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'guru_id' => $guruMapel->id,
            'tahun_pelajaran_id' => $tapel->id,
            'jam_pelajaran' => 2,
        ]);
        $siswa = Siswa::create([
            'nis' => '2001',
            'nisn' => '2234567890',
            'nama' => 'Siti',
            'jenis_kelamin' => 'P',
            'tgl_lahir' => '2010-01-01',
            'status' => 'Aktif',
            'user_id' => $this->admin->id,
            'nama_penambah' => $this->admin->name,
        ]);

        $response = $this->actingAs($this->admin)->post(route('nilai.store'), [
            'tahun_pelajaran_id' => $tapel->id,
            'nilai' => [
                $siswa->id => [
                    $kelasMapel->id => [
                        'pts' => 80,
                        'pas' => 90,
                    ],
                ],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('nilai', [
            'siswa_id' => $siswa->id,
            'kelas_mapel_id' => $kelasMapel->id,
            'guru_id' => $guruMapel->id,
            'pts' => 80,
            'pas' => 90,
        ]);
    }

    public function test_admin_can_store_high_nilai_with_full_arabic_predikat(): void
    {
        $guruMapel = Guru::create([
            'nip' => '5555555555',
            'nama' => 'Guru Predikat',
        ]);
        $tapel = TahunPelajaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'aktif' => true,
        ]);
        $kelas = Kelas::create([
            'nama_kelas' => 'III A',
            'tingkat' => 3,
            'guru_id' => $guruMapel->id,
            'jumlah_siswa' => 10,
            'tahun_pelajaran' => '2025/2026 - Ganjil',
            'user_id' => $this->admin->id,
            'nama_penambah' => $this->admin->name,
        ]);
        $mapel = MataPelajaran::create([
            'kode_mapel' => 'DIN',
            'nama_mapel' => 'Dinniyyah',
            'kkm' => 75,
            'kelompok' => 'A',
            'jenis_rapot' => 'dinniyyah',
        ]);
        $kelasMapel = KelasMapel::create([
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'guru_id' => $guruMapel->id,
            'tahun_pelajaran_id' => $tapel->id,
            'jam_pelajaran' => 2,
        ]);
        $siswa = Siswa::create([
            'nis' => '4001',
            'nisn' => '4234567890',
            'nama' => 'Zaid',
            'jenis_kelamin' => 'L',
            'tgl_lahir' => '2010-01-01',
            'status' => 'Aktif',
            'user_id' => $this->admin->id,
            'nama_penambah' => $this->admin->name,
        ]);
        Nilai::create([
            'siswa_id' => $siswa->id,
            'kelas_mapel_id' => $kelasMapel->id,
            'tahun_pelajaran_id' => $tapel->id,
            'guru_id' => $guruMapel->id,
            'rph' => 93,
        ]);

        $response = $this->actingAs($this->admin)->post(route('nilai.store'), [
            'tahun_pelajaran_id' => $tapel->id,
            'nilai' => [
                $siswa->id => [
                    $kelasMapel->id => [
                        'rph' => 93,
                        'pts' => 100,
                        'pas' => 100,
                    ],
                ],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('nilai', [
            'siswa_id' => $siswa->id,
            'kelas_mapel_id' => $kelasMapel->id,
            'hpa' => 96.5,
            'predikat' => '(Mumtaz) ممتاز',
        ]);
    }

    public function test_admin_can_store_uh_with_kelas_mapel_guru(): void
    {
        Guru::create([
            'nip' => '3333333333',
            'nama' => 'Guru Lain',
        ]);
        $guruMapel = Guru::create([
            'nip' => '4444444444',
            'nama' => 'Guru UH',
        ]);
        $tapel = TahunPelajaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'aktif' => true,
        ]);
        $kelas = Kelas::create([
            'nama_kelas' => 'II A',
            'tingkat' => 2,
            'guru_id' => $guruMapel->id,
            'jumlah_siswa' => 10,
            'tahun_pelajaran' => '2025/2026 - Ganjil',
            'user_id' => $this->admin->id,
            'nama_penambah' => $this->admin->name,
        ]);
        $mapel = MataPelajaran::create([
            'kode_mapel' => 'IPS',
            'nama_mapel' => 'IPS',
            'kkm' => 75,
            'kelompok' => 'A',
            'jenis_rapot' => 'akademik',
        ]);
        $kelasMapel = KelasMapel::create([
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'guru_id' => $guruMapel->id,
            'tahun_pelajaran_id' => $tapel->id,
            'jam_pelajaran' => 2,
            'jumlah_uh' => 2,
        ]);
        $siswa = Siswa::create([
            'nis' => '3001',
            'nisn' => '3234567890',
            'nama' => 'Amin',
            'jenis_kelamin' => 'L',
            'tgl_lahir' => '2010-01-01',
            'status' => 'Aktif',
            'user_id' => $this->admin->id,
            'nama_penambah' => $this->admin->name,
        ]);

        $response = $this->actingAs($this->admin)->post(route('nilai.uh.store'), [
            'tahun_pelajaran_id' => $tapel->id,
            'nilai' => [
                $siswa->id => [
                    $kelasMapel->id => [
                        'uh1' => 70,
                        'uh2' => 90,
                    ],
                ],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('nilai', [
            'siswa_id' => $siswa->id,
            'kelas_mapel_id' => $kelasMapel->id,
            'guru_id' => $guruMapel->id,
            'rata_uh' => 80,
            'rph' => 80,
        ]);
    }

    public function test_admin_can_print_student_and_class_rapot_after_input_nilai(): void
    {
        $guruMapel = Guru::create([
            'nip' => '6666666666',
            'nama' => 'Guru Rapot',
        ]);
        $tapel = TahunPelajaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'aktif' => true,
        ]);
        $kelas = Kelas::create([
            'nama_kelas' => 'IV A',
            'tingkat' => 4,
            'guru_id' => $guruMapel->id,
            'jumlah_siswa' => 1,
            'tahun_pelajaran' => '2025/2026 - Ganjil',
            'user_id' => $this->admin->id,
            'nama_penambah' => $this->admin->name,
        ]);
        $mapel = MataPelajaran::create([
            'kode_mapel' => 'RPT',
            'nama_mapel' => 'Mapel Rapot',
            'kkm' => 75,
            'kelompok' => 'A',
            'jenis_rapot' => 'akademik',
        ]);
        $kelasMapel = KelasMapel::create([
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'guru_id' => $guruMapel->id,
            'tahun_pelajaran_id' => $tapel->id,
            'jam_pelajaran' => 2,
        ]);
        $siswa = Siswa::create([
            'nis' => '5001',
            'nisn' => '5234567890',
            'nama' => 'Hasan',
            'jenis_kelamin' => 'L',
            'tgl_lahir' => '2010-01-01',
            'status' => 'Aktif',
            'user_id' => $this->admin->id,
            'nama_penambah' => $this->admin->name,
        ]);
        RiwayatKelas::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $tapel->id,
            'status' => 'aktif',
        ]);
        Nilai::create([
            'siswa_id' => $siswa->id,
            'kelas_mapel_id' => $kelasMapel->id,
            'tahun_pelajaran_id' => $tapel->id,
            'guru_id' => $guruMapel->id,
            'rph' => 90,
            'pts' => 90,
            'pas' => 90,
            'hpa' => 90,
            'predikat' => '(Jayyid Jiddan) جيد جدا',
        ]);

        $this->actingAs($this->admin)
            ->get(route('rapot.cetak.print', [$siswa->id, $tapel->id, 'akademik']))
            ->assertOk()
            ->assertSee('Hasan')
            ->assertSee('Mapel Rapot');

        $this->actingAs($this->admin)
            ->get(route('rapot.cetak.kelas', [$tapel->id, $kelas->id, 'akademik']))
            ->assertOk()
            ->assertSee('HASAN')
            ->assertSee('Mapel Rapot');
    }

    public function test_admin_can_register_new_student_to_initial_class(): void
    {
        $guru = Guru::create([
            'nip' => '7777777777',
            'nama' => 'Guru Registrasi',
        ]);
        $tapel = TahunPelajaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'aktif' => true,
        ]);
        $kelas = Kelas::create([
            'nama_kelas' => 'I B',
            'tingkat' => 1,
            'guru_id' => $guru->id,
            'jumlah_siswa' => 0,
            'tahun_pelajaran' => '2025/2026 - Ganjil',
            'user_id' => $this->admin->id,
            'nama_penambah' => $this->admin->name,
        ]);
        $siswa = Siswa::create([
            'nis' => '6001',
            'nisn' => '6234567890',
            'nama' => 'Siswa Baru',
            'jenis_kelamin' => 'L',
            'tgl_lahir' => '2010-01-01',
            'status' => 'Aktif',
            'user_id' => $this->admin->id,
            'nama_penambah' => $this->admin->name,
        ]);

        $response = $this->actingAs($this->admin)->post(route('kenaikan-kelas.store'), [
            'mode' => 'registrasi',
            'tahun_baru_id' => $tapel->id,
            'kelas_baru_id' => $kelas->id,
            'siswa_ids' => [$siswa->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('riwayat_kelas', [
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'tahun_pelajaran_id' => $tapel->id,
            'status' => 'aktif',
        ]);
        $this->assertDatabaseHas('kelas', [
            'id' => $kelas->id,
            'jumlah_siswa' => 1,
        ]);
    }

    public function test_admin_can_access_nilai_audit_route(): void
    {
        TahunPelajaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'aktif' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('nilai.audit'));

        $response->assertStatus(200);
    }

    public function test_admin_can_move_student_to_another_class_in_same_year(): void
    {
        $guru = Guru::create([
            'nip' => '8888888888',
            'nama' => 'Guru Pindah',
        ]);
        $tapel = TahunPelajaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'aktif' => true,
        ]);
        $kelasAsal = Kelas::create([
            'nama_kelas' => 'II B',
            'tingkat' => 2,
            'guru_id' => $guru->id,
            'jumlah_siswa' => 1,
            'tahun_pelajaran' => '2025/2026 - Ganjil',
            'user_id' => $this->admin->id,
            'nama_penambah' => $this->admin->name,
        ]);
        $kelasTujuan = Kelas::create([
            'nama_kelas' => 'II C',
            'tingkat' => 2,
            'guru_id' => $guru->id,
            'jumlah_siswa' => 0,
            'tahun_pelajaran' => '2025/2026 - Ganjil',
            'user_id' => $this->admin->id,
            'nama_penambah' => $this->admin->name,
        ]);
        $siswa = Siswa::create([
            'nis' => '7001',
            'nisn' => '7234567890',
            'nama' => 'Siswa Pindah',
            'jenis_kelamin' => 'P',
            'tgl_lahir' => '2010-01-01',
            'status' => 'Aktif',
            'user_id' => $this->admin->id,
            'nama_penambah' => $this->admin->name,
        ]);
        RiwayatKelas::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelasAsal->id,
            'tahun_pelajaran_id' => $tapel->id,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->admin)->post(route('kenaikan-kelas.store'), [
            'mode' => 'pindah',
            'tahun_baru_id' => $tapel->id,
            'kelas_lama_id' => $kelasAsal->id,
            'kelas_baru_id' => $kelasTujuan->id,
            'siswa_ids' => [$siswa->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('riwayat_kelas', [
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelasTujuan->id,
            'tahun_pelajaran_id' => $tapel->id,
            'status' => 'aktif',
        ]);
        $this->assertDatabaseHas('kelas', [
            'id' => $kelasAsal->id,
            'jumlah_siswa' => 0,
        ]);
        $this->assertDatabaseHas('kelas', [
            'id' => $kelasTujuan->id,
            'jumlah_siswa' => 1,
        ]);
    }
}
