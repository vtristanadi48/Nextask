<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $hasInvalidNisn = false;

        DB::table('siswa_profiles')
            ->select(['id', 'nisn'])
            ->orderBy('id')
            ->chunkById(500, function ($profiles) use (&$hasInvalidNisn): bool {
                foreach ($profiles as $profile) {
                    if (! is_string($profile->nisn) || preg_match('/\A[0-9]{10}\z/', $profile->nisn) !== 1) {
                        $hasInvalidNisn = true;

                        return false;
                    }
                }

                return true;
            });

        $hasDuplicateNisn = DB::table('siswa_profiles')
            ->select('nisn')
            ->whereNotNull('nisn')
            ->groupBy('nisn')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($hasInvalidNisn || $hasDuplicateNisn) {
            throw new RuntimeException(
                'Setiap profil siswa harus memiliki NISN unik 10 digit sebelum migration dijalankan. Lengkapi data siswa lama terlebih dahulu.',
            );
        }

        Schema::table('siswa_profiles', function (Blueprint $table) {
            $table->string('nisn', 10)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa_profiles', function (Blueprint $table) {
            $table->string('nisn', 10)->nullable()->change();
        });
    }
};
