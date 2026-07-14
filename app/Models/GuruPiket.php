<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuruPiket extends Model
{
    protected $table = 'guru_piket';

    protected $fillable = ['hari', 'guru_id', 'urutan'];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
}
