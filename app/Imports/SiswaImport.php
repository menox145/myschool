<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\RiwayatKelas;
use App\Models\TahunPelajaran;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsEmptyRows, WithEvents
{
    use SkipsFailures;

    protected $userId;
    protected $userName;
    protected $importRows = [];
    protected ?TahunPelajaran $tapelAktif = null;

    public function __construct(int $userId, string $userName)
    {
        $this->userId = $userId;
        $this->userName = $userName;
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function model(array $row)
    {
        if (empty($row['nis']) || empty($row['nisn']) || empty($row['nama'])) {
            return null;
        }

        $this->tapelAktif ??= TahunPelajaran::where('aktif', 1)->first();
        if (! $this->tapelAktif) {
            return null;
        }

        $kelasNama = trim((string) $row['kelas']);
        $tahunPelajaranLabel = $this->tapelAktif->tahun . ' - ' . $this->tapelAktif->semester;

        // AUTO CREATE KELAS KALAU BELUM ADA
        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => $kelasNama],
            [
                'tingkat' => $this->guessTingkat($kelasNama),
                'jumlah_siswa' => 0,
                'user_id' => $this->userId,
                'guru_id' => null,
                'nama_penambah' => $this->userName,
                'tahun_pelajaran' => $tahunPelajaranLabel,
            ]
        );

        $this->importRows[] = [
            'nis' => trim($row['nis']),
            'kelas_id' => $kelas->id,
        ];

        return new Siswa([
            'nis' => trim($row['nis']),
            'nisn' => trim($row['nisn']),
            'nama' => trim($row['nama']),
            'jenis_kelamin' => strtoupper(trim($row['jk'])),
            'status' => trim($row['status']) ?? 'Aktif',
            'tgl_lahir' => $this->transformDate($row['tgl_lahir']),
            'alamat' => $row['alamat'] ?? null,
            'no_hp' => $row['no_hp'] ?? null,
            'nama_ayah' => $row['nama_ayah'] ?? null,
            'nama_ibu' => $row['nama_ibu'] ?? null,
            'tahun_masuk' => $row['tahun_masuk'] ?? date('Y'),
            'user_id' => $this->userId,
            'nama_penambah' => $this->userName,
        ]);
    }

    public function rules(): array
    {
        return [
            'nis' => 'required|unique:siswas,nis',
            'nisn' => 'required|unique:siswas,nisn',
            'nama' => 'required',
            'jk' => 'required|in:L,P,l,p',
            'kelas' => 'required', // HAPUS exists, biar bisa auto create
            'status' => 'required|in:Aktif,Lulus,Pindah,Drop Out',
            'tgl_lahir' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nis.required' => 'NIS wajib diisi',
            'nis.unique' => 'NIS :input sudah ada',
            'nisn.unique' => 'NISN :input sudah ada',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function () {
                $tapel = $this->tapelAktif ?? TahunPelajaran::where('aktif', 1)->first();
                if (! $tapel) {
                    return;
                }

                foreach ($this->importRows as $row) {
                    $siswa = Siswa::where('nis', $row['nis'])->first();
                    if (! $siswa) {
                        continue;
                    }

                    RiwayatKelas::firstOrCreate(
                        [
                            'siswa_id' => $siswa->id,
                            'tahun_pelajaran_id' => $tapel->id,
                        ],
                        [
                            'kelas_id' => $row['kelas_id'],
                            'status' => 'aktif',
                        ]
                    );
                }

                $kelasIds = collect($this->importRows)->pluck('kelas_id')->unique();
                foreach ($kelasIds as $kelasId) {
                    Kelas::where('id', $kelasId)->update([
                        'jumlah_siswa' => RiwayatKelas::where('kelas_id', $kelasId)
                            ->where('tahun_pelajaran_id', $tapel->id)
                            ->where('status', 'aktif')
                            ->count(),
                    ]);
                }
            },
        ];
    }

    private function transformDate($value)
    {
        if (empty($value)) return null;
        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
        }
        return date('Y-m-d', strtotime(str_replace('/', '-', $value)));
    }

    private function guessTingkat(string $kelas): int
    {
        if (preg_match('/\b([1-6]|I{1,3}|IV|V|VI)\b/i', $kelas, $matches)) {
            $value = strtoupper($matches[1]);
            return match ($value) {
                'I' => 1,
                'II' => 2,
                'III' => 3,
                'IV' => 4,
                'V' => 5,
                'VI' => 6,
                default => (int) $value,
            };
        }

        return 1;
    }
}
