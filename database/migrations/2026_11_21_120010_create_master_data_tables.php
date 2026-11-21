<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jurusans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_jurusan', 30)->unique();
            $table->string('nama_jurusan');
            $table->timestamps();
        });

        Schema::create('mata_pelajarans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mapel', 30)->unique();
            $table->string('nama_mapel');
            $table->timestamps();
        });

        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas')->unique();
            $table->string('tingkat', 10);
            $table->foreignId('jurusan_id')->constrained('jurusans')->restrictOnDelete();
            $table->foreignId('wali_kelas_id')->nullable()->constrained('guru_profiles')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('guru_kelas_mapels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_profile_id')->constrained('guru_profiles')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(
                ['guru_profile_id', 'kelas_id', 'mata_pelajaran_id'],
                'guru_kelas_mapels_assignment_unique',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guru_kelas_mapels');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('mata_pelajarans');
        Schema::dropIfExists('jurusans');
    }
};
