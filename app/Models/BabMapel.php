<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BabMapel extends Model
{
    protected $table = 'bab_mapel';
    protected $fillable = ['kelas_mapel_id', 'nama_bab', 'urutan'];

    public function subBab()
    {
        return $this->hasMany(SubBabMapel::class, 'bab_mapel_id')->orderBy('urutan');
    }
}
