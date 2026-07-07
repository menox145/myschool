<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $fillable = [
        'nis',
        'nisn',
        'nama',
        'jenis_kelamin',
        'tgl_lahir',
        'alamat',
        'no_hp',
        // 'kelas_id', <- HAPUS INI
        'foto',
        'user_id',
        'nama_penambah',
        'nama_ayah',
        'nama_ibu',
        'pekerjaan_ayah',
        'pekerjaan_ibu',
        'penghasilan_ayah',
        'penghasilan_ibu',
        'anak_ke',
        'tahun_masuk',
        'status'
    ];

    // HAPUS SEMUA RELASI KELAS LAMA, PAKE INI AJA
    public function riwayatKelas()
    {
        return $this->hasMany(RiwayatKelas::class);
    }

    public function kelasAktif()
    {
        $tahunAktif = TahunPelajaran::where('aktif', 1)->first();
        return $this->hasOne(RiwayatKelas::class)
            ->where('tahun_pelajaran_id', $tahunAktif?->id)
            ->where('status', 'aktif')
            ->with('kelas.waliKelas');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class, 'siswa_id', 'id');
    }
}
