<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Nilai;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\RiwayatKelas;

class RapotController extends Controller
{
    public function index(Request $request)
    {
        $allTapel = TahunPelajaran::orderBy('tahun', 'desc')->get();
        $tapelSelected = $request->tahun_pelajaran_id;
        $kelas = collect();
        $siswas = collect();

        if ($tapelSelected) {
            $kelas = Kelas::whereHas('kelasMapel', function ($q) use ($tapelSelected) {
                $q->where('tahun_pelajaran_id', $tapelSelected);
            })->orderBy('nama_kelas')->get();
        }

        if ($tapelSelected && $request->kelas_id) {
            // GANTI: Ambil siswa dari riwayat_kelas, bukan dari siswa.kelas_id
            $siswas = Siswa::whereHas('riwayatKelas', function ($q) use ($request, $tapelSelected) {
                $q->where('kelas_id', $request->kelas_id)
                    ->where('tahun_pelajaran_id', $tapelSelected)
                    ->where('status', 'aktif');
            })
                ->whereHas('nilai', function ($q) use ($tapelSelected) {
                    $q->where('tahun_pelajaran_id', $tapelSelected);
                })
                ->orderBy('nama')
                ->get();
        }

        return view('rapot.index', compact('allTapel', 'tapelSelected', 'kelas', 'siswas'));
    }

    public function cetak($siswa_id, $tahun_pelajaran_id, $jenis_rapot)
    {
        $siswa = Siswa::findOrFail($siswa_id);
        $tahun = TahunPelajaran::findOrFail($tahun_pelajaran_id);

        // GANTI: Ambil kelas dari riwayat, bukan $siswa->kelas
        $riwayat = RiwayatKelas::with('kelas.waliKelas')
            ->where('siswa_id', $siswa_id)
            ->where('tahun_pelajaran_id', $tahun_pelajaran_id)
            ->firstOrFail();

        $nilai = Nilai::join('kelas_mapel', 'nilai.kelas_mapel_id', '=', 'kelas_mapel.id')
            ->join('mata_pelajaran', 'kelas_mapel.mapel_id', '=', 'mata_pelajaran.id')
            ->where('nilai.siswa_id', $siswa_id)
            ->where('nilai.tahun_pelajaran_id', $tahun_pelajaran_id)
            ->where('mata_pelajaran.jenis_rapot', $jenis_rapot)
            ->select('nilai.*', 'mata_pelajaran.nama_mapel as mapel_nama', 'mata_pelajaran.urutan', 'mata_pelajaran.kkm')
            ->orderBy('mata_pelajaran.urutan')
            ->get();

        $rata_rata = $nilai->whereNotNull('hpa')->avg('hpa');
        $rata_rata = $rata_rata ? round($rata_rata, 2) : '-';

        $view = match ($jenis_rapot) {
            'akademik' => 'rapot.cetak-akademik',
            'dinniyyah' => 'rapot.cetak-dinniyyah',
            'tahfidz' => 'rapot.cetak-tahfidz',
            default => 'rapot.cetak-akademik'
        };

        // Kirim $riwayat ke view, bukan $siswa->kelas
        return view($view, compact('siswa', 'tahun', 'jenis_rapot', 'nilai', 'rata_rata', 'riwayat'));
    }

    public function cetakKelas($tahun_pelajaran_id, $kelas_id, $jenis_rapot)
    {
        $kelas = Kelas::with('waliKelas')->findOrFail($kelas_id);
        $tahun = TahunPelajaran::findOrFail($tahun_pelajaran_id);

        // GANTI: Ambil siswa dari riwayat_kelas
        $riwayatSiswas = RiwayatKelas::with('siswa')
            ->where('kelas_id', $kelas_id)
            ->where('tahun_pelajaran_id', $tahun_pelajaran_id)
            ->where('status', 'aktif')
            ->whereHas('siswa.nilai', function ($q) use ($tahun_pelajaran_id, $jenis_rapot) {
                $q->where('tahun_pelajaran_id', $tahun_pelajaran_id)
                    ->whereHas('kelasMapel.mapel', fn($m) => $m->where('jenis_rapot', $jenis_rapot));
            })
            ->get();

        $dataRapot = [];
        foreach ($riwayatSiswas as $riwayat) {
            $siswa = $riwayat->siswa;
            $nilai = Nilai::join('kelas_mapel', 'nilai.kelas_mapel_id', '=', 'kelas_mapel.id')
                ->join('mata_pelajaran', 'kelas_mapel.mapel_id', '=', 'mata_pelajaran.id')
                ->where('nilai.siswa_id', $siswa->id)
                ->where('nilai.tahun_pelajaran_id', $tahun_pelajaran_id)
                ->where('mata_pelajaran.jenis_rapot', $jenis_rapot)
                ->select('nilai.*', 'mata_pelajaran.nama_mapel as mapel_nama', 'mata_pelajaran.urutan', 'mata_pelajaran.kkm')
                ->orderBy('mata_pelajaran.urutan')
                ->get();

            $dataRapot[] = [
                'siswa' => $siswa,
                'nilai' => $nilai,
                'rata_rata' => $nilai->whereNotNull('hpa')->avg('hpa') ? round($nilai->avg('hpa'), 2) : '-',
            ];
        }

        $view = match ($jenis_rapot) {
            'akademik' => 'rapot.cetak-kelas-akademik',
            'dinniyyah' => 'rapot.cetak-kelas-dinniyyah',
            'tahfidz' => 'rapot.cetak-kelas-tahfidz',
            default => 'rapot.cetak-kelas-akademik'
        };

        return view($view, compact('kelas', 'tahun', 'jenis_rapot', 'dataRapot'));
    }
}
