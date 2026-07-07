<?php

namespace App\Http\Controllers;

use App\Models\Nilai;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunPelajaran;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\NilaiImport;
use App\Models\KelasMapel;
use App\Models\RiwayatKelas;

class NilaiController extends Controller
{
    public function index(Request $request)
    {
        $tapelAktif = TahunPelajaran::where('aktif', true)->first();

        if (!$tapelAktif) {
            return redirect()->route('tahun-pelajaran.index')
                ->with('error', 'Belum ada Tahun Pelajaran yang aktif! Aktifkan dulu.');
        }

        $kelas = Kelas::orderBy('nama_kelas')->get();

        $jenisRapot = ['akademik', 'dinniyyah', 'tahfidz'];
        $kelasSelected = $request->kelas_id;
        $jenisSelected = $request->jenis_rapot;

        // Ambil semua mapel dalam 1 jenis rapot di kelas itu
        $kelasMapelList = collect();
        $siswa = collect();
        $nilaiSiswa = collect();

        if ($kelasSelected && $jenisSelected) {
            $kelasMapelList = KelasMapel::with(['mapel', 'guru'])
                ->where('kelas_id', $kelasSelected)
                ->where('tahun_pelajaran_id', $tapelAktif->id)
                ->whereHas('mapel', fn($q) => $q->where('jenis_rapot', $jenisSelected))
                ->join('mata_pelajaran', 'kelas_mapel.mapel_id', '=', 'mata_pelajaran.id')
                ->orderBy('mata_pelajaran.urutan')
                ->select('kelas_mapel.*')
                ->get();

            if ($kelasMapelList->count() > 0) {
                $siswa = $this->siswaByKelas($kelasSelected, $tapelAktif->id);

                // Ambil nilai: siswa_id => [kelas_mapel_id => nilai]
                $nilaiSiswa = Nilai::whereIn('kelas_mapel_id', $kelasMapelList->pluck('id'))
                    ->where('tahun_pelajaran_id', $tapelAktif->id)
                    ->get()
                    ->groupBy('siswa_id')
                    ->map(fn($items) => $items->keyBy('kelas_mapel_id'));
            }
        }

        return view('dashboard.nilai', compact(
            'tapelAktif',
            'kelas',
            'jenisRapot',
            'kelasSelected',
            'jenisSelected',
            'kelasMapelList',
            'siswa',
            'nilaiSiswa'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajaran,id',
            'nilai' => 'required|array',
        ]);

        foreach ($request->nilai as $siswa_id => $mapelData) {
            foreach ($mapelData as $km_id => $data) {
                $kelasMapel = KelasMapel::find($km_id);
                if (! $kelasMapel) {
                    continue;
                }

                // Ambil nilai lama biar RPH nggak kehapus
                $nilaiLama = Nilai::where('siswa_id', $siswa_id)
                    ->where('kelas_mapel_id', $km_id)
                    ->where('tahun_pelajaran_id', $request->tahun_pelajaran_id)
                    ->first();

                $rph = $nilaiLama->rph ?? $nilaiLama->rata_uh ?? 0; // Ambil dari UH
                $pts = $data['pts'] ?? $nilaiLama->pts ?? 0;
                $pas = $data['pas'] ?? $nilaiLama->pas ?? 0;

                // Skip kalau PTS & PAS kosong dan RPH juga 0
                if ($pts == 0 && $pas == 0 && $rph == 0) continue;

                // Hitung Nilai Akhir Kurmer: (2*RPH + PTS + PAS) / 4
                $hpa = null;
                if ($rph > 0 || $pts > 0 || $pas > 0) {
                    $hpa = round((2 * $rph + $pts + $pas) / 4, 2);
                }

                $pre = $hpa ? $this->hitungPredikat($hpa) : null;

                Nilai::updateOrCreate(
                    [
                        'siswa_id' => $siswa_id,
                        'kelas_mapel_id' => $km_id,
                        'tahun_pelajaran_id' => $request->tahun_pelajaran_id,
                    ],
                    [
                        'guru_id' => $kelasMapel->guru_id,
                        'rph' => $rph, // Jangan diupdate, ambil dari UH
                        'pts' => $pts,
                        'pas' => $pas,
                        'hpa' => $hpa,
                        'predikat' => $pre,
                    ]
                );
            }
        }

        return back()->with('success', 'Nilai PTS & PAS berhasil disimpan!');
    }

    private function hitungPredikat($nilai)
    {
        if ($nilai >= 91) return '(Mumtaz) ممتاز';
        if ($nilai >= 81) return '(Jayyid Jiddan) جيد جدا';
        if ($nilai >= 71) return '(Jayyid) جيد';
        if ($nilai >= 61) return '(Maqbul) مقبول';
        return '(Rasib) راسب';
    }


    // Download template Excel
    public function downloadTemplate(Request $request)
    {
        $request->validate(['kelas_mapel_id' => 'required|exists:kelas_mapel,id']);

        $km = KelasMapel::with('kelas', 'mapel')->find($request->kelas_mapel_id);
        $siswa = $this->siswaByKelas($km->kelas_id, $km->tahun_pelajaran_id);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'nis');
        $sheet->setCellValue('B1', 'nama');
        $sheet->setCellValue('C1', 'rph');
        $sheet->setCellValue('D1', 'pts');
        $sheet->setCellValue('E1', 'pas');

        $row = 2;
        foreach ($siswa as $s) {
            $sheet->setCellValue('A' . $row, $s->nis);
            $sheet->setCellValue('B' . $row, $s->nama);
            $row++;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'template_nilai_' . $km->mapel->kode_mapel . '_' . date('Ymd') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        $writer->save('php://output');
        exit;
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'kelas_mapel_id' => 'required',
            'tahun_pelajaran_id' => 'required',
        ]);

        Excel::import(new NilaiImport($request->kelas_mapel_id, $request->tahun_pelajaran_id), $request->file('file'));
    }
    // INI UNTUK UH - pake uh.blade.php
    public function indexUh(Request $request)
    {
        $tapelAktif = TahunPelajaran::where('aktif', true)->first();

        if (!$tapelAktif) {
            return redirect()->route('tahun-pelajaran.index')
                ->with('error', 'Belum ada Tahun Pelajaran yang aktif! Aktifkan dulu.');
        }

        $kelas = Kelas::orderBy('nama_kelas')->get();
        $kelasSelected = $request->kelas_id;

        $siswa = collect();
        $allMapelKelas = collect();

        if ($kelasSelected) {
            $allMapelKelas = KelasMapel::with(['mapel', 'nilai' => function ($q) use ($tapelAktif) {
                $q->where('tahun_pelajaran_id', $tapelAktif->id);
            }])
                ->where('kelas_id', $kelasSelected)
                ->where('tahun_pelajaran_id', $tapelAktif->id)
                ->join('mata_pelajaran', 'kelas_mapel.mapel_id', '=', 'mata_pelajaran.id')
                ->orderBy('mata_pelajaran.urutan')
                ->select('kelas_mapel.*')
                ->get();

            $siswa = $this->siswaByKelas($kelasSelected, $tapelAktif->id);
        }

        return view('dashboard.uh', compact(
            'kelas',
            'kelasSelected',
            'siswa',
            'allMapelKelas',
            'tapelAktif'
        ));
    }

    public function storeUh(Request $request)
    {
        $request->validate([
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajaran,id',
            'nilai' => 'nullable|array',
        ]);

        if (!$request->nilai) {
            return back()->with('error', 'Tidak ada nilai yang diinput');
        }

        foreach ($request->nilai as $siswa_id => $mapelData) {
            foreach ($mapelData as $kelas_mapel_id => $data) {
                $km = KelasMapel::find($kelas_mapel_id);
                if (!$km) continue;

                $jumlahUh = $km->jumlah_uh ?? 3;
                $uh = [];
                $updateData = [];

                for ($i = 1; $i <= 6; $i++) {
                    $val = ($i <= $jumlahUh) ? ($data['uh' . $i] ?? null) : null;
                    $updateData['uh' . $i] = $val;

                    if ($i <= $jumlahUh && $val !== null && $val !== '') {
                        $uh[] = floatval($val);
                    }
                }

                if (count($uh) == 0) continue;

                $rata_uh = round(array_sum($uh) / count($uh), 2);
                $updateData['rata_uh'] = $rata_uh;
                $updateData['rph'] = $rata_uh;
                $updateData['guru_id'] = $km->guru_id;

                Nilai::updateOrCreate(
                    [
                        'siswa_id' => $siswa_id,
                        'kelas_mapel_id' => $kelas_mapel_id,
                        'tahun_pelajaran_id' => $request->tahun_pelajaran_id,
                    ],
                    $updateData
                );
            }
        }

        return back()->with('success', 'Nilai UH berhasil disimpan');
    }

    private function siswaByKelas($kelasId, $tahunPelajaranId)
    {
        return Siswa::whereHas('riwayatKelas', function ($q) use ($kelasId, $tahunPelajaranId) {
            $q->where('kelas_id', $kelasId)
                ->where('tahun_pelajaran_id', $tahunPelajaranId)
                ->where('status', 'aktif');
        })->orderBy('nama')->get();
    }
}
