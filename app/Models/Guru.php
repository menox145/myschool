<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    protected $fillable = [
        'nip',
        'nama',
        'tgl_lahir',
        'no_hp',
        'email',
        'nik',
        'no_kk',
        'foto'
    ];

    protected $table = 'gurus';

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'guru_id');
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class, 'guru_id');
    }

    public function kelasMapel()
    {
        return $this->hasMany(KelasMapel::class, 'guru_id');
    }
}
