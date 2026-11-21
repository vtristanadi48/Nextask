<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_kelas', 'tingkat', 'jurusan_id', 'wali_kelas_id'])]
class Kelas extends Model
{
    protected $table = 'kelas';

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(GuruProfile::class, 'wali_kelas_id');
    }

    public function siswaProfiles(): HasMany
    {
        return $this->hasMany(SiswaProfile::class);
    }

    public function guruKelasMapels(): HasMany
    {
        return $this->hasMany(GuruKelasMapel::class);
    }
}
