<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Exports\KelasExport;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Guru;
use Illuminate\Support\Facades\Storage;
use App\Models\Siswa;
use App\Models\TahunPelajaran;
use App\Exports\GuruExport;
use App\Exports\SiswaExport;
use App\Imports\SiswaImport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\RiwayatKelas; // <- TAMBAH INI
use App\Models\Nilai;







class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    }

    public function siswa(Request $request)
    {
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $tahunAktif = TahunPelajaran::where('aktif', 1)->first();

        $siswa = Siswa::with(['kelasAktif.kelas']) // <- TAMBAH .kelas
            ->when($request->kelas_id && $tahunAktif, function ($q) use ($request, $tahunAktif) {
                $q->whereHas('riwayatKelas', function ($sub) use ($request, $tahunAktif) {
                    $sub->where('kelas_id', $request->kelas_id)
                        ->where('tahun_pelajaran_id', $tahunAktif->id)
                        ->where('status', 'aktif');
                });
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->jk, function ($q) use ($request) {
                $q->where('jenis_kelamin', $request->jk);
            })
            ->when($request->search, function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                    ->orWhere('nis', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->get();

        return view('dashboard.siswa', compact('siswa', 'kelas'));
    }

    /**
     * Tampilkan riwayat perkembangan siswa dari tahun ke tahun
     */
    public function riwayatIndex(Request $request)
    {
        // Ambil daftar filter
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $tahunList = TahunPelajaran::orderBy('tahun', 'desc')->get();

        // Query siswa beserta riwayat, dengan opsi filter kelas/tahun
        $query = Siswa::with(['riwayatKelas.kelas', 'riwayatKelas.tahunPelajaran']);

        if ($request->kelas_id || $request->tahun_pelajaran_id) {
            $query->whereHas('riwayatKelas', function ($q) use ($request) {
                if ($request->kelas_id) {
                    $q->where('kelas_id', $request->kelas_id);
                }
                if ($request->tahun_pelajaran_id) {
                    $q->where('tahun_pelajaran_id', $request->tahun_pelajaran_id);
                }
            });
        }

        $siswa = $query->orderBy('nama')->get();

        // Ambil semua nilai yang relevan agar bisa ditampilkan per riwayat
        $siswaIds = $siswa->pluck('id')->toArray();
        $tahunIds = $tahunList->pluck('id')->toArray();

        $nilaiIndex = collect();
        if (!empty($siswaIds)) {
            $nilais = Nilai::whereIn('siswa_id', $siswaIds)
                ->whereIn('tahun_pelajaran_id', $tahunIds)
                ->get();

            // group by key siswaId|tahunId for quick lookup in view
            $nilaiIndex = $nilais->groupBy(function ($n) {
                return $n->siswa_id . '|' . $n->tahun_pelajaran_id;
            });
        }

        return view('dashboard.siswa-riwayat', compact('siswa', 'kelasList', 'tahunList', 'nilaiIndex'));
    }



    public function storeSiswa(Request $request)
    {
        $data = $request->validate([
            'nis' => 'required|numeric|digits_between:1,15|unique:siswas,nis',
            'nisn' => 'required|numeric|digits:10|unique:siswas,nisn',
            'nama' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'status' => 'required|in:Aktif,Lulus,Pindah,Drop Out',
            'tgl_lahir' => 'required|date',
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
        ], [
            'nis.required' => 'NIS wajib diisi',
            'nis.numeric' => 'NIS harus angka',
            'nis.unique' => 'NIS sudah terdaftar',
            'nis.digits_between' => 'NIS maksimal 15 digit',
            'nisn.required' => 'NISN wajib diisi',
            'nisn.numeric' => 'NISN harus angka',
            'nisn.digits' => 'NISN harus 10 digit',
            'nisn.unique' => 'NISN sudah terdaftar',
            'nama.required' => 'Nama wajib diisi',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'status.required' => 'Status wajib dipilih',
            'tgl_lahir.required' => 'Tanggal lahir wajib diisi',
            'tgl_lahir.date' => 'Format tanggal lahir tidak valid',
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
            'nis' => 'required|numeric|digits_between:1,15|unique:siswas,nis,' . $siswa->id,
            'nisn' => 'required|numeric|digits:10|unique:siswas,nisn,' . $siswa->id,
            'nama' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'tgl_lahir' => 'required|date',
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
            'status' => 'required|in:Aktif,Lulus,Pindah,Drop Out',
        ], [
            'nis.required' => 'NIS wajib diisi',
            'nis.numeric' => 'NIS harus angka',
            'nis.unique' => 'NIS sudah dipakai siswa lain',
            'nis.digits_between' => 'NIS maksimal 15 digit',
            'nisn.required' => 'NISN wajib diisi',
            'nisn.numeric' => 'NISN harus angka',
            'nisn.digits' => 'NISN harus 10 digit',
            'nisn.unique' => 'NISN sudah dipakai siswa lain',
            'nama.required' => 'Nama wajib diisi',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'status.required' => 'Status wajib dipilih',
            'tgl_lahir.required' => 'Tanggal lahir wajib diisi',
            'tgl_lahir.date' => 'Format tanggal lahir tidak valid',
        ]);

        if ($request->hasFile('foto')) {
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
            if (! TahunPelajaran::where('aktif', 1)->exists()) {
                return back()->with('error', 'Import gagal: aktifkan Tahun Pelajaran dulu.');
            }

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
        $siswa = Siswa::with('kelasAktif.kelas') // <- TAMBAH .kelas
            ->when($search, function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            })
            ->get();

        $pdf = Pdf::loadView('dashboard.siswa-pdf', compact('siswa'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('data-siswa-' . date('Y-m-d') . '.pdf');
    }

    public function downloadTemplateSiswa()
    {
        $export = new class implements FromArray, WithHeadings {
            public function array(): array
            {
                return [];
            }

            public function headings(): array
            {
                return [
                    'nis',
                    'nisn',
                    'nama',
                    'jk',
                    'kelas',
                    'status',
                    'tgl_lahir',
                    'alamat',
                    'no_hp',
                    'nama_ayah',
                    'nama_ibu',
                    'tahun_masuk',
                ];
            }
        };

        return Excel::download($export, 'template-siswa.xlsx');
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
        $request->validate(
            [
                'nip' => 'required|numeric|digits_between:10,20|unique:gurus,nip', // <- UBAH INI
                'nama' => 'required|string|max:100',
                'tgl_lahir' => 'nullable|date',
                'no_hp' => 'nullable|string|max:15',
                'email' => 'nullable|email|max:100',
                'nik' => 'nullable|numeric|digits:16|unique:gurus,nik',
                'no_kk' => 'nullable|numeric|digits:16',
                'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ],
            [
                'nip.required' => 'NIP wajib diisi',
                'nip.numeric' => 'NIP harus angka',
                'nip.digits_between' => 'NIP minimal 10 digit, maksimal 20 digit', // <- UBAH INI
                'nip.unique' => 'NIP sudah terdaftar',
                'nik.numeric' => 'NIK harus angka',
                'nik.digits' => 'NIK harus 16 digit',
                'no_kk.numeric' => 'No KK harus angka',
                'no_kk.digits' => 'No KK harus 16 digit',
            ]
        );

        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('guru', 'public');
        }

        Guru::create($data);

        return redirect()->route('guru')->with('success', 'Data guru berhasil ditambahkan!');
    }

    public function updateGuru(Request $request, int $id)
    {
        $guru = Guru::findOrFail($id);
        $request->validate([
            'nip' => 'required|numeric|digits_between:10,20|unique:gurus,nip,' . $id, // <- UBAH INI
            'nama' => 'required|string|max:100',
            'tgl_lahir' => 'nullable|date',
            'no_hp' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:100',
            'nik' => 'nullable|numeric|digits:16|unique:gurus,nik,' . $id,
            'no_kk' => 'nullable|numeric|digits:16',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'nip.required' => 'NIP wajib diisi',
            'nip.numeric' => 'NIP harus angka',
            'nip.digits_between' => 'NIP minimal 10 digit, maksimal 20 digit', // <- UBAH INI
            'nip.unique' => 'NIP sudah dipakai guru lain',
            'nik.numeric' => 'NIK harus angka',
            'nik.digits' => 'NIK harus 16 digit',
            'no_kk.numeric' => 'No KK harus angka',
            'no_kk.digits' => 'No KK harus 16 digit',
        ]);

        $data = $request->except(['foto', '_token', '_method']);

        if ($request->hasFile('foto')) {
            if ($guru->foto) {
                Storage::disk('public')->delete($guru->foto);
            }
            $data['foto'] = $request->file('foto')->store('guru', 'public');
        }

        $guru->update($data);

        return redirect()->route('guru')->with('success', 'Data guru berhasil diupdate!');
    }

    public function destroyGuru(int $id)
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
            'tingkat' => 'required|integer|min:1|max:6',
            'guru_id' => 'required|exists:gurus,id',
            'jumlah_siswa' => 'required|integer|min:0',
            'tahun_pelajaran' => 'required|string',
        ]);

        // Pastin yang dipilih ada di master tahun_pelajaran
        if (! $this->tahunPelajaranLabelExists($request->tahun_pelajaran)) {
            return back()->withErrors(['tahun_pelajaran' => 'Pilih tahun pelajaran yang valid'])->withInput();
        }

        Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'tingkat' => $request->tingkat,
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
            'tingkat' => 'required|integer|min:1|max:6',
            'guru_id' => 'required|exists:gurus,id',
            'jumlah_siswa' => 'required|integer|min:0',
            'tahun_pelajaran' => 'required|string',
        ]);

        if (! $this->tahunPelajaranLabelExists($request->tahun_pelajaran)) {
            return back()->withErrors(['tahun_pelajaran' => 'Pilih tahun pelajaran yang valid'])->withInput();
        }

        $kelas->update([
            'nama_kelas' => $request->nama_kelas,
            'tingkat' => $request->tingkat,
            'guru_id' => $request->guru_id,
            'jumlah_siswa' => $request->jumlah_siswa,
            'tahun_pelajaran' => $request->tahun_pelajaran,
        ]);

        return redirect()->route('kelas')->with('success', 'Data kelas berhasil diupdate!');
    }

    private function tahunPelajaranLabelExists(string $label): bool
    {
        [$tahun, $semester] = array_pad(array_map('trim', explode('-', $label, 2)), 2, null);

        if (! $tahun || ! $semester) {
            return false;
        }

        return TahunPelajaran::where('tahun', $tahun)
            ->where('semester', $semester)
            ->exists();
    }

    public function destroyKelas(Kelas $kelas)
    {
        $kelas->delete();
        return redirect()->route('kelas')->with('success', 'Data kelas berhasil dihapus!');
    }
}
