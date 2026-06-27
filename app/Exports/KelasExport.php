<?php

namespace App\Exports;


use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KelasExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        return Kelas::with('user')
            ->when($this->search, function ($q, $search) {
                return $q->where('nama_kelas', 'like', "%{$search}%")
                    ->orWhere('wali_kelas', 'like', "%{$search}%")
                    ->orWhere('tahun_pelajaran', 'like', "%{$search}%");
            })->latest()->get();
    }

    public function headings(): array
    {
        return ['No', 'Nama Kelas', 'Wali Kelas', 'Jumlah Siswa', 'Tahun Pelajaran', 'Ditambahkan Oleh', 'Tanggal Dibuat'];
    }

    public function map($k): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $k->nama_kelas,
            $k->wali_kelas,
            $k->jumlah_siswa,
            $k->tahun_pelajaran,
            $k->nama_penambah ?? '-',
            $k->created_at->format('d/m/Y H:i'),
        ];
    }
}
