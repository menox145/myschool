<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\GuruPiket;
use Illuminate\Http\Request;

class GuruPiketController extends Controller
{
    public function index()
    {
        $piket = GuruPiket::with('guru')->orderBy('urutan')->get();
        $guruList = Guru::orderBy('nama')->get();

        return view('dashboard.guru-piket', compact('piket', 'guruList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'guru_id' => 'nullable|exists:gurus,id',
            'urutan' => 'required|integer|min:1',
        ]);

        GuruPiket::create($request->only(['hari', 'guru_id', 'urutan']));

        return back()->with('success', 'Jadwal piket berhasil ditambahkan.');
    }

    public function update(Request $request, GuruPiket $guruPiket)
    {
        $request->validate([
            'hari' => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'guru_id' => 'nullable|exists:gurus,id',
            'urutan' => 'required|integer|min:1',
        ]);

        $guruPiket->update($request->only(['hari', 'guru_id', 'urutan']));

        return back()->with('success', 'Jadwal piket berhasil diupdate.');
    }

    public function destroy(GuruPiket $guruPiket)
    {
        $guruPiket->delete();

        return back()->with('success', 'Jadwal piket berhasil dihapus.');
    }
}
