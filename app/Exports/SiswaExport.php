<?php

namespace App\Exports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SiswaExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        return Siswa::with('kelas')
            ->when($this->request->search, function ($q) {
                $q->where('nama', 'like', "%{$this->request->search}%")
                    ->orWhere('nis', 'like', "%{$this->request->search}%");
            })
            ->when($this->request->jk, function ($q) {
                $q->where('jenis_kelamin', $this->request->jk);
            })
            ->when($this->request->kelas_id, function ($q) {
                $q->where('kelas_id', $this->request->kelas_id);
            })
            ->when($this->request->status, function ($q) {
                $q->where('status', $this->request->status);
            })
            ->orderBy('kelas_id')
            ->orderBy('nama')
            ->get();
    }

    public function headings(): array
    {
        return ['nis', 'nisn', 'nama', 'jk', 'kelas', 'status', 'tgl_lahir', 'alamat', 'no_hp', 'nama_ayah', 'nama_ibu', 'tahun_masuk'];
    }

    public function map($siswa): array
    {
        return [
            $siswa->nis,
            $siswa->nisn,
            $siswa->nama,
            $siswa->jenis_kelamin,
            $siswa->kelas->nama_kelas ?? '-',
            $siswa->status,
            $siswa->tgl_lahir ? date('Y-m-d', strtotime($siswa->tgl_lahir)) : '',
            $siswa->alamat,
            $siswa->no_hp,
            $siswa->nama_ayah,
            $siswa->nama_ibu,
            $siswa->tahun_masuk,
        ];
    }
}
