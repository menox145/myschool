<?php

namespace App\Http\Controllers;

use App\Models\TahunPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TahunPelajaranController extends Controller
{
    public function index()
    {
        $tapel = TahunPelajaran::orderBy('tahun', 'desc')
            ->orderBy('semester', 'asc')
            ->get();
        return view('dashboard.tahun-pelajaran', compact('tapel'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tahun' => 'required|string|max:9', // 2025/2026
            'semester' => 'required|in:Ganjil,Genap',
            'tgl_mulai' => 'nullable|date',
            'tgl_selesai' => 'nullable|date|after_or_equal:tgl_mulai',
            'aktif' => 'nullable|boolean'
        ]);

        DB::transaction(function () use ($data, $request) {
            // Kalo centang aktif, nonaktifin yang lain dulu
            if ($request->has('aktif')) {
                TahunPelajaran::where('aktif', true)->update(['aktif' => false]);
                $data['aktif'] = true;
            } else {
                $data['aktif'] = false;
            }

            TahunPelajaran::create($data);
        });

        return redirect()->route('tahun-pelajaran.index')->with('success', 'Tahun Pelajaran berhasil ditambah!');
    }

    public function update(Request $request, TahunPelajaran $tahun_pelajaran)
    {
        $data = $request->validate([
            'tahun' => 'required|string|max:9',
            'semester' => 'required|in:Ganjil,Genap',
            'tgl_mulai' => 'nullable|date',
            'tgl_selesai' => 'nullable|date|after_or_equal:tgl_mulai',
            'aktif' => 'nullable|boolean'
        ]);

        DB::transaction(function () use ($data, $request, $tahun_pelajaran) {
            if ($request->has('aktif')) {
                TahunPelajaran::where('id', '!=', $tahun_pelajaran->id)->update(['aktif' => false]);
                $data['aktif'] = true;
            } else {
                $data['aktif'] = false;
            }

            $tahun_pelajaran->update($data);
        });

        return redirect()->route('tahun-pelajaran.index')->with('success', 'Tahun Pelajaran berhasil diupdate!');
    }

    public function destroy(TahunPelajaran $tahun_pelajaran)
    {
        // Cek kalo udah ada nilai, jangan hapus
        // if ($tahun_pelajaran->nilais()->exists()) {
        //     return back()->with('error', 'Tidak bisa hapus! Sudah ada data nilai di semester ini.');
        // }

        $tahun_pelajaran->delete();
        return redirect()->route('tahun-pelajaran.index')->with('success', 'Tahun Pelajaran berhasil dihapus!');
    }
}
