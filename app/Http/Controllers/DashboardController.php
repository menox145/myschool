<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Exports\KelasExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Guru;
use Illuminate\Support\Facades\Storage;
use App\Models\Siswa;
use App\Models\TahunPelajaran;
use App\Exports\GuruExport;
use App\Exports\SiswaExport;
use App\Imports\SiswaImport;
use Barryvdh\DomPDF\Facade\Pdf;






class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    }

    public function siswa(Request $request)
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();

        $siswa = Siswa::with('kelas')
            ->when($request->search, function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                    ->orWhere('nis', 'like', "%{$request->search}%");
            })
            ->when($request->jk, function ($q) use ($request) {
                $q->where('jenis_kelamin', $request->jk);
            })
            ->when($request->kelas_id, function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->latest()->get();

        return view('dashboard.siswa', compact('siswa', 'kelas'));
    }


    public function storeSiswa(Request $request)
    {
        $data = $request->validate([
            'nis' => 'required|string|max:20|unique:siswas,nis',
            'nisn' => 'required|string|max:20|unique:siswas,nisn', // WAJIB
            'nama' => 'required|string|max:100', // Nama boleh sama
            'jenis_kelamin' => 'required|in:L,P', // WAJIB
            'kelas_id' => 'required|exists:kelas,id', // WAJIB
            'status' => 'required|in:Aktif,Lulus,Pindah,Drop Out', // WAJIB
            'tgl_lahir' => 'required|date', // WAJIB
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:15',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'nama_ayah' => 'nullable|string|max:100',
            'nama_ibu' => 'nullable|string|max:100',
            'pekerjaan_ayah' => 'nullable|string|max:100',
            'pekerjaan_ibu' => 'nullable|string|max:100',
            'penghasilan_ayah' => 'nullable|integer|min:0',
            'penghasilan_ibu' => 'nullable|integer|min:0',
            'anak_ke' => 'nullable|integer|min:1',
            'tahun_masuk' => 'nullable|digits:4|integer|min:2000|max:' . date('Y'),
        ]);

        $data['user_id'] = Auth::id();
        $data['nama_penambah'] = Auth::user()->name;

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('siswa', 'public');
        }

        Siswa::create($data);
        return redirect()->route('siswa')->with('success', 'Data siswa berhasil ditambahkan!');
    }
    public function updateSiswa(Request $request, Siswa $siswa)
    {
        $data = $request->validate([
            'nis' => 'required|string|max:20|unique:siswas,nis,' . $siswa->id,
            'nama' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'tgl_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:15',
            'kelas_id' => 'required|exists:kelas,id',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'nama_ayah' => 'nullable|string|max:100',
            'nama_ibu' => 'nullable|string|max:100',
            'pekerjaan_ayah' => 'nullable|string|max:100',
            'pekerjaan_ibu' => 'nullable|string|max:100',
            'penghasilan_ayah' => 'nullable|integer|min:0',
            'penghasilan_ibu' => 'nullable|integer|min:0',
            'anak_ke' => 'nullable|integer|min:1',
            'tahun_masuk' => 'nullable|digits:4|integer|min:2000|max:' . date('Y'),
            'status' => 'required|in:Aktif,Lulus,Pindah,Drop Out',
        ]);

        if ($request->hasFile('foto')) {
            // Hapus foto lama
            if ($siswa->foto) {
                Storage::disk('public')->delete($siswa->foto);
            }
            $data['foto'] = $request->file('foto')->store('siswa', 'public');
        }

        $siswa->update($data);
        return redirect()->route('siswa')->with('success', 'Data siswa berhasil diupdate!');
    }

    public function destroySiswa(Siswa $siswa)
    {
        // Hapus foto kalo ada
        if ($siswa->foto) {
            Storage::disk('public')->delete($siswa->foto);
        }

        $siswa->delete();
        return redirect()->route('siswa')->with('success', 'Data siswa berhasil dihapus!');
    }
    public function exportSiswa(Request $request)
    {
        return Excel::download(new SiswaExport($request), 'data-siswa-' . date('Y-m-d') . '.xlsx');
    }

    public function importSiswa(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);

        try {
            $import = new SiswaImport(Auth::id(), Auth::user()->name);
            Excel::import($import, $request->file('file'));

            if ($import->failures()->isNotEmpty()) {
                $errors = $import->failures()->map(function ($f) {
                    return 'Baris ' . $f->row() . ': ' . implode(', ', $f->errors());
                })->toArray();
                return back()->with('error', implode(' | ', $errors));
            }

            return back()->with('success', 'Import berhasil! ' . Siswa::count() . ' total siswa');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }




    public function printSiswa(Request $request)
    {
        $search = $request->search;
        $siswa = Siswa::with('kelas')
            ->when($search, function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            })
            ->get();

        $pdf = Pdf::loadView('dashboard.siswa-pdf', compact('siswa'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('data-siswa-' . date('Y-m-d') . '.pdf');
    }



    public function guru(Request $request)
    {
        $search = $request->search;
        $guru = Guru::when($search, function ($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%")
                ->orWhere('nip', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('no_hp', 'like', "%{$search}%");
        })
            ->latest()
            ->get();

        return view('dashboard.guru', compact('guru', 'search'));
    }

    public function exportGuru(Request $request)
    {
        return Excel::download(new GuruExport($request->search), 'data-guru-' . date('Y-m-d') . '.xlsx');
    }

    public function storeGuru(Request $request)
    {
        $request->validate([
            'nip' => 'required|string|max:20|unique:gurus,nip',
            'nama' => 'required|string|max:100',
            'tgl_lahir' => 'nullable|date',
            'no_hp' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:100',
            'nik' => 'nullable|digits:16|unique:gurus,nik', // ganti ini
            'no_kk' => 'nullable|digits:16', // ganti ini
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('guru', 'public');
        }

        Guru::create($data);

        return redirect()->route('guru')->with('success', 'Data guru berhasil ditambahkan!');
    }
    public function updateGuru(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        $request->validate([
            'nip' => 'required|string|max:20|unique:gurus,nip,' . $id,
            'nama' => 'required|string|max:100',
            'tgl_lahir' => 'nullable|date',
            'no_hp' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:100',
            'nik' => 'nullable|digits:16|unique:gurus,nik,' . $id,
            'no_kk' => 'nullable|digits:16',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // TAMBAHIN INI - LOGIC UPDATE
        $data = $request->except(['foto', '_token', '_method']);

        if ($request->hasFile('foto')) {
            // Hapus foto lama kalo ada
            if ($guru->foto) {
                Storage::disk('public')->delete($guru->foto);
            }
            $data['foto'] = $request->file('foto')->store('guru', 'public');
        }

        $guru->update($data);

        return redirect()->route('guru')->with('success', 'Data guru berhasil diupdate!');
    }

    public function destroyGuru($id)
    {
        $guru = Guru::findOrFail($id);
        if ($guru->foto) Storage::disk('public')->delete($guru->foto);
        $guru->delete();
        return redirect()->route('guru')->with('success', 'Data guru berhasil dihapus!');
    }


    public function kelas(Request $request)
    {
        $search = $request->input('search');
        $guru = Guru::orderBy('nama')->get();
        $tahunPelajaran = TahunPelajaran::orderBy('tahun', 'desc')->orderBy('semester')->get(); // tambah ini

        $kelas = Kelas::with(['user', 'guru'])
            ->when($search, function ($query, $search) {
                return $query->where('nama_kelas', 'like', "%{$search}%")
                    ->orWhereHas('guru', function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%");
                    })
                    ->orWhere('tahun_pelajaran', 'like', "%{$search}%");
            })
            ->latest()->get();

        return view('dashboard.kelas', compact('kelas', 'search', 'guru', 'tahunPelajaran')); // kirim $tahunPelajaran
    }

    public function storeKelas(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:50|unique:kelas,nama_kelas',
            'guru_id' => 'required|exists:gurus,id',
            'jumlah_siswa' => 'required|integer|min:0',
            'tahun_pelajaran' => 'required|string',
        ]);

        // Pastin yang dipilih ada di master tahun_pelajaran
        $tapelExists = TahunPelajaran::whereRaw("CONCAT(tahun, ' - ', semester) = ?", [$request->tahun_pelajaran])->exists();
        if (!$tapelExists) {
            return back()->withErrors(['tahun_pelajaran' => 'Pilih tahun pelajaran yang valid'])->withInput();
        }

        Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'guru_id' => $request->guru_id,
            'jumlah_siswa' => $request->jumlah_siswa,
            'tahun_pelajaran' => $request->tahun_pelajaran,
            'user_id' => Auth::id(),
            'nama_penambah' => Auth::user()->name,
        ]);

        return redirect()->route('kelas')->with('success', 'Data kelas berhasil ditambahkan!');
    }
    public function exportKelas(Request $request)
    {
        return Excel::download(new KelasExport($request->search), 'data-kelas.xlsx');
    }
    public function updateKelas(Request $request, Kelas $kelas)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:50|unique:kelas,nama_kelas,' . $kelas->id,
            'guru_id' => 'required|exists:gurus,id',
            'jumlah_siswa' => 'required|integer|min:0',
            'tahun_pelajaran' => 'required|string',
        ]);

        $tapelExists = TahunPelajaran::whereRaw("CONCAT(tahun, ' - ', semester) = ?", [$request->tahun_pelajaran])->exists();
        if (!$tapelExists) {
            return back()->withErrors(['tahun_pelajaran' => 'Pilih tahun pelajaran yang valid'])->withInput();
        }

        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'guru_id' => $request->guru_id,
            'jumlah_siswa' => $request->jumlah_siswa,
            'tahun_pelajaran' => $request->tahun_pelajaran,
        ]);

        return redirect()->route('kelas')->with('success', 'Data kelas berhasil diupdate!');
    }

    public function destroyKelas(Kelas $kelas)
    {
        $kelas->delete();
        return redirect()->route('kelas')->with('success', 'Data kelas berhasil dihapus!');
    }
}
