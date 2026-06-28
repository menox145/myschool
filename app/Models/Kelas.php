<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $fillable = [
        'nama_kelas',
        'guru_id', // TAMBAH INI
        'jumlah_siswa',
        'tahun_pelajaran',
        'user_id',
        'nama_penambah'
    ];
    protected $table = 'kelas';

    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'guru_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'kelas_id', 'id');
    }
    public function kelasMapel()
    {
        return $this->hasMany(KelasMapel::class, 'kelas_id');
    }
}
