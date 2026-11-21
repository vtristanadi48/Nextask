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
        Schema::table('kelas', function (Blueprint $table) {
            $table->foreignId('jurusan_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('kelas')->whereNull('jurusan_id')->exists()) {
            throw new RuntimeException('Cannot require a jurusan for kelas while rows have no jurusan_id.');
        }

        Schema::table('kelas', function (Blueprint $table) {
            $table->foreignId('jurusan_id')->nullable(false)->change();
        });
    }
};
