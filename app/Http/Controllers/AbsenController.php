<?php

namespace App\Http\Controllers;

use App\Models\Absen;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunPelajaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsenController extends Controller
{
    public function index(Request $request)
    {
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $tahunAktif = TahunPelajaran::where('aktif', true)->first();
        $kelasSelected = $request->kelas_id;
        $tanggal = $request->tanggal ?: now()->toDateString();

        $siswa = collect();
        $absenBySiswa = collect();

        if ($kelasSelected && $tahunAktif) {
            $siswa = Siswa::whereHas('riwayatKelas', function ($q) use ($kelasSelected, $tahunAktif) {
                $q->where('kelas_id', $kelasSelected)
                    ->where('tahun_pelajaran_id', $tahunAktif->id)
                    ->where('status', 'aktif');
            })->orderBy('nama')->get();

            $absenBySiswa = Absen::where('kelas_id', $kelasSelected)
                ->where('tahun_pelajaran_id', $tahunAktif->id)
                ->whereDate('tanggal', $tanggal)
                ->get()
                ->keyBy('siswa_id');
        }

        return view('absen.index', compact(
            'kelas',
            'tahunAktif',
            'kelasSelected',
            'tanggal',
            'siswa',
            'absenBySiswa'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal' => 'required|date',
            'absen' => 'required|array',
            'absen.*.status' => 'required|in:Hadir,Sakit,Izin,Alpa',
            'absen.*.keterangan' => 'nullable|string|max:255',
        ]);

        $tahunAktif = TahunPelajaran::where('aktif', true)->first();
        if (! $tahunAktif) {
            return back()->with('error', 'Tahun Pelajaran aktif belum diset.');
        }

        $siswaIds = Siswa::whereHas('riwayatKelas', function ($q) use ($data, $tahunAktif) {
            $q->where('kelas_id', $data['kelas_id'])
                ->where('tahun_pelajaran_id', $tahunAktif->id)
                ->where('status', 'aktif');
        })->pluck('id')->all();

        foreach ($data['absen'] as $siswaId => $item) {
            if (! in_array((int) $siswaId, $siswaIds, true)) {
                continue;
            }

            Absen::updateOrCreate(
                [
                    'siswa_id' => $siswaId,
                    'tanggal' => $data['tanggal'],
                    'tahun_pelajaran_id' => $tahunAktif->id,
                ],
                [
                    'kelas_id' => $data['kelas_id'],
                    'status' => $item['status'],
                    'keterangan' => $item['keterangan'] ?? null,
                    'user_id' => Auth::id(),
                ]
            );
        }

        return redirect()
            ->route('absen.index', ['kelas_id' => $data['kelas_id'], 'tanggal' => $data['tanggal']])
            ->with('success', 'Absen siswa berhasil disimpan.');
    }

    public function cetak(Request $request)
    {
        $data = $request->validate([
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $tahun = TahunPelajaran::findOrFail($data['tahun_pelajaran_id']);
        $kelas = Kelas::with('waliKelas')->findOrFail($data['kelas_id']);
        $siswa = Siswa::whereHas('riwayatKelas', function ($q) use ($data) {
            $q->where('kelas_id', $data['kelas_id'])
                ->where('tahun_pelajaran_id', $data['tahun_pelajaran_id'])
                ->where('status', 'aktif');
        })->orderBy('nama')->get();

        $absens = Absen::where('kelas_id', $data['kelas_id'])
            ->where('tahun_pelajaran_id', $data['tahun_pelajaran_id'])
            ->whereBetween('tanggal', [$data['tanggal_mulai'], $data['tanggal_selesai']])
            ->get()
            ->groupBy('siswa_id');

        $pdf = Pdf::loadView('absen.cetak-pdf', [
            'tahun' => $tahun,
            'kelas' => $kelas,
            'siswa' => $siswa,
            'absens' => $absens,
            'tanggalMulai' => $data['tanggal_mulai'],
            'tanggalSelesai' => $data['tanggal_selesai'],
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('absen-' . $kelas->nama_kelas . '-' . $data['tanggal_mulai'] . '.pdf');
    }
}
