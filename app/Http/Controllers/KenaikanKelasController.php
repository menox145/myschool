<?php

namespace App\Http\Controllers;

use App\Models\RiwayatKelas;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KenaikanKelasController extends Controller
{
    public function index()
    {
        $allTapel = TahunPelajaran::orderByDesc('tahun')->get();
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        return view('dashboard.kenaikan-kelas', compact('allTapel', 'kelas'));
    }

    public function getSiswaByKelas(Request $request)
    {
        if ($request->mode === 'registrasi') {
            $data = Siswa::whereDoesntHave('riwayatKelas', function ($q) use ($request) {
                $q->where('tahun_pelajaran_id', $request->tahun_id);
            })
                ->where('status', 'Aktif')
                ->orderBy('nama')
                ->get()
                ->map(fn($siswa) => ['siswa' => $siswa]);

            return response()->json($data);
        }

        $data = RiwayatKelas::with('siswa')
            ->where('tahun_pelajaran_id', $request->tahun_id)
            ->where('kelas_id', $request->kelas_id)
            ->where('status', 'aktif')
            ->get();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $mode = $request->input('mode', 'kenaikan');

        $rules = [
            'mode' => 'required|in:registrasi,pindah,kenaikan',
            'tahun_baru_id' => 'required|exists:tahun_pelajaran,id',
            'siswa_ids' => 'required|array|min:1',
            'siswa_ids.*' => 'exists:siswas,id',
        ];

        if ($mode === 'kenaikan') {
            $rules['tahun_lama_id'] = 'required|exists:tahun_pelajaran,id|different:tahun_baru_id';
            $rules['kelas_lama_id'] = 'required|exists:kelas,id';
            $rules['kelas_baru_id'] = 'nullable|exists:kelas,id';
        } elseif ($mode === 'pindah') {
            $rules['kelas_lama_id'] = 'required|exists:kelas,id';
            $rules['kelas_baru_id'] = 'required|exists:kelas,id|different:kelas_lama_id';
        } else {
            $rules['kelas_baru_id'] = 'required|exists:kelas,id';
        }

        $request->validate($rules);

        if ($mode === 'registrasi') {
            return $this->registrasiAwal($request);
        }

        if ($mode === 'pindah') {
            return $this->pindahKelas($request);
        }

        $kelasLama = Kelas::find($request->kelas_lama_id);
        $kelasBaru = $request->kelas_baru_id ? Kelas::find($request->kelas_baru_id) : null;

        // Validasi: ga boleh turun kelas, kecuali lulus dari kelas 6
        if ($kelasLama->tingkat != 6 && (! $kelasBaru || $kelasBaru->tingkat <= $kelasLama->tingkat)) {
            return back()->with('error', 'Kelas tujuan harus lebih tinggi dari kelas asal!');
        }

        DB::beginTransaction();
        try {
            foreach ($request->siswa_ids as $siswaId) {
                $statusLama = $kelasLama->tingkat == 6 ? 'lulus' : 'aktif';

                RiwayatKelas::where('siswa_id', $siswaId)
                    ->where('tahun_pelajaran_id', $request->tahun_lama_id)
                    ->where('kelas_id', $request->kelas_lama_id)
                    ->update(['status' => $statusLama]);

                if ($kelasLama->tingkat == 6) {
                    Siswa::where('id', $siswaId)->update([
                        'status' => 'Lulus',
                    ]);
                } else {
                    Siswa::where('id', $siswaId)->update([
                        'status' => 'Aktif',
                    ]);

                    RiwayatKelas::updateOrCreate(
                        [
                            'siswa_id' => $siswaId,
                            'tahun_pelajaran_id' => $request->tahun_baru_id,
                        ],
                        [
                            'kelas_id' => $request->kelas_baru_id,
                            'status' => 'aktif'
                        ]
                    );
                }
            }

            $this->syncJumlahSiswa([$request->kelas_lama_id, $request->kelas_baru_id], $request->tahun_lama_id);
            $this->syncJumlahSiswa([$request->kelas_baru_id], $request->tahun_baru_id);

            DB::commit();
            return back()->with('success', count($request->siswa_ids) . ' siswa berhasil diproses!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    private function registrasiAwal(Request $request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->siswa_ids as $siswaId) {
                RiwayatKelas::updateOrCreate(
                    [
                        'siswa_id' => $siswaId,
                        'tahun_pelajaran_id' => $request->tahun_baru_id,
                    ],
                    [
                        'kelas_id' => $request->kelas_baru_id,
                        'status' => 'aktif',
                    ]
                );

                Siswa::where('id', $siswaId)->update(['status' => 'Aktif']);
            }

            $this->syncJumlahSiswa([$request->kelas_baru_id], $request->tahun_baru_id);

            DB::commit();
            return back()->with('success', count($request->siswa_ids) . ' siswa berhasil diregistrasikan ke kelas!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    private function pindahKelas(Request $request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->siswa_ids as $siswaId) {
                RiwayatKelas::updateOrCreate(
                    [
                        'siswa_id' => $siswaId,
                        'tahun_pelajaran_id' => $request->tahun_baru_id,
                    ],
                    [
                        'kelas_id' => $request->kelas_baru_id,
                        'status' => 'aktif',
                    ]
                );
            }

            $this->syncJumlahSiswa([$request->kelas_lama_id, $request->kelas_baru_id], $request->tahun_baru_id);

            DB::commit();
            return back()->with('success', count($request->siswa_ids) . ' siswa berhasil dipindahkan kelas!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    private function syncJumlahSiswa(array $kelasIds, int $tahunPelajaranId): void
    {
        foreach (array_unique(array_filter($kelasIds)) as $kelasId) {
            Kelas::where('id', $kelasId)->update([
                'jumlah_siswa' => RiwayatKelas::where('kelas_id', $kelasId)
                    ->where('tahun_pelajaran_id', $tahunPelajaranId)
                    ->where('status', 'aktif')
                    ->count(),
            ]);
        }
    }
}
