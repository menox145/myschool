<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubBabMapel extends Model
{
    protected $table = 'sub_bab_mapel';
    protected $fillable = ['bab_mapel_id', 'nama_sub_bab', 'urutan'];

    public function bab() // INI YANG DIPAKE
    {
        return $this->belongsTo(BabMapel::class, 'bab_mapel_id');
    }

    public function nilaiHarian()
    {
        return $this->hasMany(NilaiHarian::class);
    }
}
