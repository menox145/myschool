<?php

namespace App\Http\Controllers;

use App\Models\KelasMapel;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use App\Models\Guru;

class KelasMapelController extends Controller
{
    public function index(Request $request)
    {
        $tahunAktif = TahunPelajaran::where('aktif', 1)->first();

        // TAMBAH INI: Redirect kalo belum ada tahun aktif
        if (!$tahunAktif) {
            return redirect()->route('tahun-pelajaran.index')
                ->with('error', 'Aktifkan Tahun Pelajaran dulu sebelum setting kelompok belajar!');
        }

        $kelasSelected = $request->kelas_id;
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $guru = \App\Models\Guru::orderBy('nama')->get();
        $allMapel = MataPelajaran::orderBy('jenis_rapot')->orderBy('urutan')->get()->groupBy('jenis_rapot');

        $kelasMapel = collect();
        if ($kelasSelected && $tahunAktif) {
            $kelasMapel = KelasMapel::where('kelas_id', $kelasSelected)
                ->where('tahun_pelajaran_id', $tahunAktif->id)
                ->with(['mapel', 'guru'])
                ->get()
                ->keyBy('mapel_id');
        }

        return view('dashboard.kelas-mapel', compact(
            'tahunAktif',
            'kelas',
            'kelasSelected',
            'kelasMapel',
            'allMapel',
            'guru'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajaran,id',
            'mapel_ids' => 'required|array|min:1',
            'mapel_ids.*' => 'exists:mata_pelajaran,id',
        ]);

        // Validasi guru_id & jam_pelajaran cuma buat mapel yg dicentang
        foreach ($request->mapel_ids as $mapelId) {
            $request->validate([
                "guru_id.{$mapelId}" => 'required|exists:gurus,id',
                "jam_pelajaran.{$mapelId}" => 'required|integer|min:1',
            ], [
                "guru_id.{$mapelId}.required" => 'Guru wajib dipilih',
                "jam_pelajaran.{$mapelId}.required" => 'Jam wajib diisi',
                "jam_pelajaran.{$mapelId}.integer" => 'Jam harus angka',
                "jam_pelajaran.{$mapelId}.min" => 'Jam minimal 1',
            ]);
        }

        foreach ($request->mapel_ids as $mapelId) {
            KelasMapel::updateOrCreate(
                [
                    'kelas_id' => $request->kelas_id,
                    'mapel_id' => $mapelId,
                    'tahun_pelajaran_id' => $request->tahun_pelajaran_id,
                ],
                [
                    'guru_id' => $request->guru_id[$mapelId],
                    'jam_pelajaran' => $request->jam_pelajaran[$mapelId],
                ]
            );
        }


        return back()->with('success', 'Kelompok belajar berhasil disimpan!');
    }

    public function update(Request $request, KelasMapel $kelasMapel)
    {
        $request->validate([
            'guru_id' => 'nullable|exists:gurus,id',
            'jam_pelajaran' => 'required|integer|min:1|max:10',
        ]);

        $kelasMapel->update($request->only(['guru_id', 'jam_pelajaran']));

        return back()->with('success', 'Data berhasil diupdate!');
    }

    public function destroy(KelasMapel $kelasMapel)
    {
        $kelasMapel->delete();
        return back()->with('success', 'Mapel berhasil dihapus dari kelas!');
    }
    public function setJumlahUh(Request $request, $id)
    {
        $request->validate(['jumlah_uh' => 'required|integer|min:1|max:6']);
        KelasMapel::find($id)->update(['jumlah_uh' => $request->jumlah_uh]);
        return response()->json(['success' => true]);
    }
}
