<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiHarian extends Model
{
    protected $table = 'nilai_harian';
    protected $fillable = [
        'siswa_id',
        'sub_bab_mapel_id',
        'kelas_mapel_id',
        'nama_mapel', // TAMBAH
        'tahun_pelajaran_id',
        'user_id',
        'nama_user', // TAMBAH
        'nilai'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function subBab()
    {
        return $this->belongsTo(SubBabMapel::class, 'sub_bab_mapel_id');
    }

    public function kelasMapel()
    {
        return $this->belongsTo(KelasMapel::class);
    }

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
