<?php

namespace App\Imports;

use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\KelasMapel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class NilaiImport implements ToModel, WithHeadingRow
{
    protected $kelas_mapel_id, $tapel_id;

    public function __construct($kelas_mapel_id, $tapel_id)
    {
        $this->kelas_mapel_id = $kelas_mapel_id;
        $this->tapel_id = $tapel_id;
    }

    public function model(array $row)
    {
        $siswa = Siswa::where('nis', $row['nis'])->first();
        if (!$siswa) return null;

        $kelasMapel = KelasMapel::find($this->kelas_mapel_id);
        if (!$kelasMapel) return null;

        $rph = $row['rph'] ?? 0;
        $pts = $row['pts'] ?? 0;
        $pas = $row['pas'] ?? 0;
        $hpa = ($rph || $pts || $pas) ? round((2 * $rph + $pts + $pas) / 4, 2) : null;

        return Nilai::updateOrCreate([
            'siswa_id' => $siswa->id,
            'kelas_mapel_id' => $this->kelas_mapel_id,
            'tahun_pelajaran_id' => $this->tapel_id,
        ], [
            'guru_id' => $kelasMapel->guru_id,
            'rph' => $rph,
            'pts' => $pts,
            'pas' => $pas,
            'hpa' => $hpa,
            'predikat' => $this->hitungPredikat($hpa),
        ]);
    }

    private function hitungPredikat($nilai)
    {
        if ($nilai === null || $nilai === 0) return null;
        if ($nilai >= 91) return '(Mumtaz) ممتاز';
        if ($nilai >= 81) return '(Jayyid Jiddan) جيد جدا';
        if ($nilai >= 71) return '(Jayyid) جيد';
        if ($nilai >= 61) return '(Maqbul) مقبول';
        return '(Rasib) راسب';
    }
}
