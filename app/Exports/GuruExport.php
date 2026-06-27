<?php

namespace App\Exports;

use App\Models\Guru;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GuruExport implements FromCollection, WithHeadings, WithMapping
{
    protected $search;

    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function collection()
    {
        return Guru::when($this->search, function ($q) {
            $q->where('nama', 'like', "%{$this->search}%")
                ->orWhere('nip', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
                ->orWhere('no_hp', 'like', "%{$this->search}%");
        })
            ->orderBy('nama')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'NIP',
            'Nama',
            'Tanggal Lahir',
            'No HP',
            'Email',
            'NIK',
            'No KK'
        ];
    }

    public function map($guru): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $guru->nip,
            $guru->nama,
            $guru->tgl_lahir,
            $guru->no_hp,
            $guru->email,
            $guru->nik,
            $guru->no_kk,
        ];
    }
}
