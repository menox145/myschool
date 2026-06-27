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
    protected $table = 'gurus'; // pastiin ini ada dan sesuai nama tabel di database
}
