<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $table = 'mata_pelajaran';

    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
        'kkm',
        'kelompok',
        'jenis_rapot',
        'urutan'
    ];

    public function nilais()
    {
        return $this->hasMany(Nilai::class, 'mapel_id');
    }
}
