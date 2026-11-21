<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['kode_jurusan', 'nama_jurusan'])]
class Jurusan extends Model
{
    protected $table = 'jurusans';

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class);
    }
}
