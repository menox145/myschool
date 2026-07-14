<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absen extends Model
{
    protected $table = 'absens';

    protected $fillable = [
        'siswa_id',
        'kelas_id',
        'tahun_pelajaran_id',
        'tanggal',
        'status',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
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
