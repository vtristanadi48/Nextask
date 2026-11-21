<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['kode_mapel', 'nama_mapel'])]
class MataPelajaran extends Model
{
    protected $table = 'mata_pelajarans';

    public function guruKelasMapels(): HasMany
    {
        return $this->hasMany(GuruKelasMapel::class, 'mata_pelajaran_id');
    }
}
