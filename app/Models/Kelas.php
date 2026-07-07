<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $fillable = [
        'nama_kelas',
        'tingkat',
        'guru_id', // TAMBAH INI
        'jumlah_siswa',
        'tahun_pelajaran',
        'user_id',
        'nama_penambah'
    ];
    protected $table = 'kelas';

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function siswa()
    {
        return $this->belongsToMany(Siswa::class, 'riwayat_kelas', 'kelas_id', 'siswa_id')
            ->withPivot(['tahun_pelajaran_id', 'status'])
            ->withTimestamps();
    }
    public function kelasMapel()
    {
        return $this->hasMany(KelasMapel::class, 'kelas_id');
    }

    // Alias for the wali kelas relation used across views/controllers
    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'guru_id', 'id');
    }
}
