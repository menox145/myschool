<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelasMapel extends Model
{
    protected $table = 'kelas_mapel';
    protected $fillable = ['kelas_id', 'mapel_id', 'guru_id', 'tahun_pelajaran_id', 'jumlah_uh'];


    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
    public function mapel()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }

    // public function guru()
    // {
    //     return $this->belongsTo(User::class, 'guru_id');
    // }
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }
    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class);
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class);
    }
}
