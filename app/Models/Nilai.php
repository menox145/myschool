<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    protected $table = 'nilai';

    protected $fillable = [
        'siswa_id',
        'kelas_mapel_id',
        'tahun_pelajaran_id',
        'guru_id',
        'uh1',
        'uh2',
        'uh3',
        'uh4',
        'uh5',
        'uh6', // WAJIB ADA
        'rata_uh', // WAJIB ADA
        'rph',
        'pts',
        'pas',
        'hpa',
        'predikat'
    ];


    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function kelasMapel()
    {
        return $this->belongsTo(KelasMapel::class, 'kelas_mapel_id');
    }
    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahun_pelajaran_id');
    }
}
