<?php

namespace App\Http\Controllers;

use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class MapelController extends Controller
{
    public function index(Request $request)
    {
        $query = MataPelajaran::query();

        if ($request->filled('kelompok')) {
            $query->where('kelompok', $request->kelompok);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_mapel', 'like', "%{$search}%")
                    ->orWhere('kode_mapel', 'like', "%{$search}%");
            });
        }

        $mapels = $query->orderBy('urutan')->get(); // GANTI JADI $mapels

        return view('dashboard.mapel', compact('mapels')); // KIRIM $mapels
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_mapel' => 'required|string|max:10|unique:mata_pelajaran',
            'nama_mapel' => 'required|string|max:100',
            'kkm' => 'required|integer|min:0|max:100',
            'kelompok' => 'required|in:A,B,C',
            'jenis_rapot' => 'required|in:akademik,dinniyyah,tahfidz',
        ]);

        $data['urutan'] = MataPelajaran::max('urutan') + 1;

        MataPelajaran::create($data);
        return redirect()->route('mapel.index')->with('success', 'Mapel berhasil ditambah!');
    }

    public function edit(MataPelajaran $mapel)
    {
        $mapels = MataPelajaran::orderBy('urutan')->get(); // AMBIL SEMUA BUAT TABLE
        return view('dashboard.mapel', compact('mapels', 'mapel')); // KIRIM 2: $mapels & $mapel
    }

    public function update(Request $request, MataPelajaran $mapel)
    {
        $data = $request->validate([
            'kode_mapel' => 'required|string|max:10|unique:mata_pelajaran,kode_mapel,' . $mapel->id,
            'nama_mapel' => 'required|string|max:100',
            'kkm' => 'required|integer|min:0|max:100',
            'kelompok' => 'required|in:A,B,C',
            'jenis_rapot' => 'required|in:akademik,dinniyyah,tahfidz', // TAMBAHIN INI, LUPA VALIDASI
        ]);

        $mapel->update($data);
        return redirect()->route('mapel.index')->with('success', 'Mapel berhasil diupdate!');
    }

    public function destroy(MataPelajaran $mapel)
    {
        $mapel->delete();
        return redirect()->route('mapel.index')->with('success', 'Mapel berhasil dihapus!');
    }
}
