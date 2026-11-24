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
        Schema::table('guru_kelas_mapels', function (Blueprint $table) {
            $table->foreignId('kelas_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('guru_kelas_mapels')->whereNull('kelas_id')->exists()) {
            throw new RuntimeException(
                'Global teacher-subject assignments must be removed before this migration can be rolled back.',
            );
        }

        Schema::table('guru_kelas_mapels', function (Blueprint $table) {
            $table->foreignId('kelas_id')->nullable(false)->change();
        });
    }
};
