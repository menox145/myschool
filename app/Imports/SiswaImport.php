<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsEmptyRows
{
    use SkipsFailures;

    protected $userId;
    protected $userName;

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

        // AUTO CREATE KELAS KALAU BELUM ADA
        $kelas = Kelas::firstOrCreate(
            ['nama_kelas' => trim($row['kelas'])],
            [
                'jumlah_siswa' => 0,
                'user_id' => $this->userId,
                'guru_id' => $this->userId,
                'nama_penambah' => $this->userName,
                'tahun_pelajaran' => '2024/2025 - Genap' // ganti sesuai kebutuhan
            ]
        );

        return new Siswa([
            'nis' => trim($row['nis']),
            'nisn' => trim($row['nisn']),
            'nama' => trim($row['nama']),
            'jenis_kelamin' => strtoupper(trim($row['jk'])),
            'kelas_id' => $kelas->id,
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

    private function transformDate($value)
    {
        if (empty($value)) return null;
        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
        }
        return date('Y-m-d', strtotime(str_replace('/', '-', $value)));
    }
}
