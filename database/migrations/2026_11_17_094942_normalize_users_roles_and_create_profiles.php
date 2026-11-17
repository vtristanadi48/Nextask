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
        if (DB::table('users')->whereNull('role')->orWhereNotIn('role', ['admin', 'guru', 'siswa'])->exists()) {
            throw new RuntimeException('All existing users must have one of the roles: admin, guru, siswa.');
        }

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        $now = now();
        DB::table('roles')->insert(array_map(
            fn (string $name): array => [
                'name' => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            ['admin', 'guru', 'siswa'],
        ));

        Schema::create('guru_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->timestamps();
        });

        Schema::create('siswa_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('nisn', 10)->nullable()->unique();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->constrained('roles')->restrictOnDelete();
        });

        $roleIds = DB::table('roles')->pluck('id', 'name');

        foreach (['admin', 'guru', 'siswa'] as $roleName) {
            DB::table('users')
                ->where('role', $roleName)
                ->update(['role_id' => $roleIds[$roleName]]);
        }

        DB::table('siswa_profiles')->insertUsing(
            ['user_id', 'nisn', 'created_at', 'updated_at'],
            DB::table('users')
                ->whereNotNull('nisn')
                ->select(['id', 'nisn', 'created_at', 'updated_at']),
        );

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->nullable(false)->change();
            $table->dropUnique(['nisn']);
            $table->dropColumn(['role', 'nisn']);
        });

        Schema::table('guru_profiles', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('siswa_profiles', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('guru');
            $table->string('nisn', 10)->nullable()->unique();
        });

        foreach (DB::table('users')->select(['id', 'role_id'])->orderBy('id')->get() as $user) {
            DB::table('users')->where('id', $user->id)->update([
                'role' => DB::table('roles')->where('id', $user->role_id)->value('name'),
                'nisn' => DB::table('siswa_profiles')->where('user_id', $user->id)->value('nisn'),
            ]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        Schema::dropIfExists('guru_profiles');
        Schema::dropIfExists('siswa_profiles');
        Schema::dropIfExists('roles');
    }
};
