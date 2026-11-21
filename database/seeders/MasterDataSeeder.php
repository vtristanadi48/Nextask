<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        Jurusan::query()->firstOrCreate(
            ['kode_jurusan' => 'PPLG'],
            ['nama_jurusan' => 'Perkembangan Perangkat Lunak dan Gim'],
        );

        foreach ([
            ['kode_mapel' => 'BING', 'nama_mapel' => 'Bahasa Inggris'],
            ['kode_mapel' => 'MTK', 'nama_mapel' => 'Matematika'],
            ['kode_mapel' => 'PWPB', 'nama_mapel' => 'Pemrograman Web dan Perangkat Bergerak'],
        ] as $subject) {
            MataPelajaran::query()->firstOrCreate(
                ['kode_mapel' => $subject['kode_mapel']],
                ['nama_mapel' => $subject['nama_mapel']],
            );
        }

        Kelas::query()->firstOrCreate(
            ['nama_kelas' => 'X PPLG 1'],
            ['tingkat' => 'X', 'jurusan_id' => null],
        );
    }
}
