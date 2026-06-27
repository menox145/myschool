<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunPelajaran extends Model
{
    protected $table = 'tahun_pelajaran'; // PAKSA PAKE NAMA INI

    protected $fillable = ['tahun', 'semester', 'tgl_mulai', 'tgl_selesai', 'aktif'];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
        'aktif' => 'boolean',
    ];

    public function nilais()
    {
        return $this->hasMany(Nilai::class);
    }
}
