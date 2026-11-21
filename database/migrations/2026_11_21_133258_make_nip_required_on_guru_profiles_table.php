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
        $hasInvalidNip = false;

        DB::table('guru_profiles')
            ->select(['id', 'nip'])
            ->orderBy('id')
            ->chunkById(500, function ($profiles) use (&$hasInvalidNip): bool {
                foreach ($profiles as $profile) {
                    if (! is_string($profile->nip) || preg_match('/\A[0-9]{18}\z/', $profile->nip) !== 1) {
                        $hasInvalidNip = true;

                        return false;
                    }
                }

                return true;
            });

        $hasDuplicateNip = DB::table('guru_profiles')
            ->select('nip')
            ->whereNotNull('nip')
            ->groupBy('nip')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($hasInvalidNip || $hasDuplicateNip) {
            throw new RuntimeException(
                'Setiap profil guru harus memiliki NIP unik 18 digit sebelum migration dijalankan. Lengkapi data guru lama terlebih dahulu.',
            );
        }

        Schema::table('guru_profiles', function (Blueprint $table) {
            $table->string('nip', 18)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru_profiles', function (Blueprint $table) {
            $table->string('nip', 50)->nullable()->change();
        });
    }
};
