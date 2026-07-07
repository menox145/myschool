<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\KelasMapel;
use App\Models\BabMapel;
use App\Models\NilaiHarian;
use App\Models\Siswa;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use App\Models\SubBabMapel;
use Illuminate\Support\Facades\Auth;

class NilaiHarianController extends Controller
{
    public function index(Request $request)
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $tahunAktif = TahunPelajaran::where('aktif', true)->first();

        $kelasSelected = $request->kelas_id;
        $mapelSelected = $request->kelas_mapel_id;

        $siswa = collect();
        $bab = collect();
        $allMapelKelas = collect();

        if ($kelasSelected && $tahunAktif) {
            $allMapelKelas = KelasMapel::with('mapel')
                ->where('kelas_id', $kelasSelected)
                ->where('tahun_pelajaran_id', $tahunAktif->id)
                ->join('mata_pelajaran', 'kelas_mapel.mapel_id', '=', 'mata_pelajaran.id')
                ->orderBy('mata_pelajaran.urutan')
                ->select('kelas_mapel.*')
                ->get();

            $siswa = Siswa::whereHas('riwayatKelas', function ($q) use ($kelasSelected, $tahunAktif) {
                $q->where('kelas_id', $kelasSelected)
                    ->where('tahun_pelajaran_id', $tahunAktif->id)
                    ->where('status', 'aktif');
            })->orderBy('nama')->get();

            if (!$mapelSelected) {
                foreach ($allMapelKelas as $km) {
                    $km->babData = BabMapel::where('kelas_mapel_id', $km->id)
                        ->with([
                            'subBab.nilaiHarian' => function ($q) use ($tahunAktif) {
                                $q->with(['user', 'siswa'])->where('tahun_pelajaran_id', $tahunAktif->id);
                            }
                        ])
                        ->orderBy('urutan')
                        ->get();
                }
            }

            if ($mapelSelected) {
                $bab = BabMapel::where('kelas_mapel_id', $mapelSelected)
                    ->with([
                        'subBab.nilaiHarian' => function ($q) use ($tahunAktif) {
                            $q->with(['user', 'siswa'])->where('tahun_pelajaran_id', $tahunAktif->id);
                        }
                    ])
                    ->orderBy('urutan')->get();
            }
        }

        return view('dashboard.harian', compact(
            'kelas',
            'siswa',
            'bab',
            'allMapelKelas',
            'kelasSelected',
            'mapelSelected',
            'tahunAktif'
        ));
    }

    public function storeBab(Request $request)
    {
        $request->validate([
            'kelas_mapel_id' => 'required|exists:kelas_mapel,id',
            'nama_bab' => 'required|string|max:100',
            'urutan' => 'required|integer|min:1',
        ]);

        $bab = BabMapel::create($request->only(['kelas_mapel_id', 'nama_bab', 'urutan']));
        return response()->json(['success' => true, 'data' => $bab]);
    }

    public function storeSubBab(Request $request)
    {
        $request->validate([
            'bab_mapel_id' => 'required|exists:bab_mapel,id',
            'nama_sub_bab' => 'required|string|max:100',
            'urutan' => 'required|integer|min:1',
        ]);

        $subBab = SubBabMapel::create($request->only(['bab_mapel_id', 'nama_sub_bab', 'urutan']));
        return response()->json(['success' => true, 'data' => $subBab]);
    }
    public function store(Request $request)
    {
        $tahunAktif = TahunPelajaran::where('aktif', true)->first();

        if (!$tahunAktif) {
            return response()->json(['success' => false, 'message' => 'Tahun pelajaran aktif belum diset']);
        }

        if ($request->has(['siswa_id', 'sub_bab_mapel_id'])) {
            // WAJIB: Ambil kelas_mapel_id dari relasi
            $kelasMapelId = SubBabMapel::join('bab_mapel', 'sub_bab_mapel.bab_mapel_id', '=', 'bab_mapel.id')
                ->where('sub_bab_mapel.id', $request->sub_bab_mapel_id)
                ->value('bab_mapel.kelas_mapel_id');

            $nilai = $request->nilai;
            if ($nilai !== null && $nilai !== '') {
                $nilai = min(100, max(0, (int)$nilai));
            }

            if ($nilai === null || $nilai === '') {
                NilaiHarian::where([
                    'siswa_id' => $request->siswa_id,
                    'sub_bab_mapel_id' => $request->sub_bab_mapel_id,
                    'tahun_pelajaran_id' => $tahunAktif->id
                ])->delete();
            } else {
                NilaiHarian::updateOrCreate(
                    [
                        'siswa_id' => $request->siswa_id,
                        'sub_bab_mapel_id' => $request->sub_bab_mapel_id,
                        'tahun_pelajaran_id' => $tahunAktif->id
                    ],
                    [
                        'nilai' => $nilai,
                        'user_id' => Auth::id(),
                        'kelas_mapel_id' => $kelasMapelId // INI JUGA MASUK DB
                    ]
                );
            }
            return response()->json(['success' => true, 'nilai' => $nilai]);
        }

        return response()->json(['success' => false]);
    }
    public function destroyBab($id)
    {
        $bab = BabMapel::findOrFail($id);
        // Hapus semua nilai harian di sub bab ini dulu
        $subBabIds = SubBabMapel::where('bab_mapel_id', $id)->pluck('id');
        NilaiHarian::whereIn('sub_bab_mapel_id', $subBabIds)->delete();
        $bab->delete();
        return response()->json(['success' => true]);
    }

    public function destroySubBab($id)
    {
        $subBab = SubBabMapel::findOrFail($id);
        NilaiHarian::where('sub_bab_mapel_id', $id)->delete();
        $subBab->delete();
        return response()->json(['success' => true]);
    }
    public function export(Request $request)
    {
        $tahunAktif = TahunPelajaran::where('aktif', true)->first();
        $kelasId = $request->kelas_id;
        $mapelId = $request->kelas_mapel_id;

        $query = NilaiHarian::with([
            'siswa.kelas',
            'user',
            'kelasMapel.mapel',
            'subBab.bab', // GANTI INI: dari babMapel ke bab
            'tahunPelajaran'
        ])
            ->where('tahun_pelajaran_id', $tahunAktif->id);

        if ($kelasId) {
            $query->whereHas('siswa.riwayatKelas', function ($q) use ($kelasId, $tahunAktif) {
                $q->where('kelas_id', $kelasId)
                    ->where('tahun_pelajaran_id', $tahunAktif->id)
                    ->where('status', 'aktif');
            });
        }

        if ($mapelId) {
            $query->where('kelas_mapel_id', $mapelId);
        }

        $nilais = $query->orderBy('updated_at', 'desc')->get();

        $filename = 'nilai_harian_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($nilais) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID',
                'Nama Siswa',
                'Kelas',
                'Mapel',
                'Bab',
                'Sub Bab',
                'Nilai',
                'Tahun Pelajaran',
                'Semester',
                'Diinput Oleh',
                'Waktu Input',
                'Terakhir Update'
            ]);

            foreach ($nilais as $n) {
                fputcsv($file, [
                    $n->id,
                    $n->siswa->nama ?? '-',
                    $n->siswa->kelasAktif?->kelas?->nama_kelas ?? '-',
                    $n->nama_mapel ?? $n->kelasMapel->mapel->nama_mapel ?? '-',
                    $n->subBab->bab->nama_bab ?? '-', // GANTI INI: dari babMapel ke bab
                    $n->subBab->nama_sub_bab ?? '-',
                    $n->nilai,
                    $n->tahunPelajaran->tahun ?? '-',
                    $n->tahunPelajaran->semester ?? '-',
                    $n->nama_user ?? $n->user->name ?? 'Data Lama',
                    $n->created_at->format('Y-m-d H:i:s'),
                    $n->updated_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
