<?php

namespace Tests\Feature;

use App\Models\Jurusan;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RoleUserSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AuthenticatedSessionTest extends TestCase
{
    use RefreshDatabase;

    public static function roleDashboardRoutes(): array
    {
        return [
            'admin' => ['admin', 'admin.dashboard', ['Dashboard', 'Administrasi', 'Akademik', 'Kelas', 'Chat', 'Pusat Bantuan']],
            'guru' => ['guru', 'guru.dashboard', ['Dashboard', 'Tahun Ajaran', 'Konten Mengajar', 'Chat', 'Pusat Bantuan']],
            'siswa' => ['siswa', 'siswa.dashboard', ['Dashboard', 'Tahun Ajaran', 'Tugas', 'Chat', 'Pusat Bantuan']],
        ];
    }

    #[DataProvider('roleDashboardRoutes')]
    public function test_a_user_is_redirected_to_the_dashboard_for_their_role(
        string $roleName,
        string $dashboardRoute,
        array $menuItems,
    ): void {
        $user = User::factory()->withRole($roleName)->create([
            'password' => 'secret-password',
        ]);

        $this->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'secret-password',
        ])
            ->assertRedirect(route($dashboardRoute))
            ->assertSessionHasNoErrors();

        $this->assertAuthenticatedAs($user);
        $dashboard = $this->get(route($dashboardRoute))
            ->assertOk()
            ->assertSee($user->name)
            ->assertSee($roleName)
            ->assertSee('aria-label="Pesan masuk"', false)
            ->assertSee('aria-label="Notifikasi sistem"', false)
            ->assertSee('aria-label="Pengaturan"', false);

        $this->assertSame(
            2,
            substr_count((string) $dashboard->getContent(), 'action="'.route('logout').'"'),
        );

        $dashboard
            ->assertSeeInOrder($menuItems)
            ->assertSee('href="#"', false)
            ->assertSee('data-feature-notice="Fitur ini sedang dalam tahap pengembangan"', false)
            ->assertSee('data-feature-toast', false);
    }

    public function test_admin_dashboard_shows_user_counts_recent_accounts_and_safe_class_count(): void
    {
        $admin = User::factory()->withRole('admin')->create([
            'name' => 'Dashboard Admin',
            'created_at' => now()->subMinutes(30),
        ]);
        User::factory()->withRole('guru')->create([
            'name' => 'Guru Terbaru',
            'email' => 'teacher.recent@example.test',
            'created_at' => now()->subMinutes(5),
        ]);
        User::factory()->withRole('guru')->create([
            'name' => 'Guru Lama',
            'email' => 'teacher.old@example.test',
            'created_at' => now()->subDays(2),
        ]);
        User::factory()->withRole('siswa')->create([
            'name' => 'Siswa Terbaru',
            'email' => 'student.recent@example.test',
            'created_at' => now()->subMinutes(2),
        ]);
        User::factory()->withRole('siswa')->create([
            'name' => 'Siswa Lama',
            'email' => 'student.old@example.test',
            'created_at' => now()->subDays(1),
        ]);
        User::factory()->withRole('siswa')->create([
            'name' => 'Siswa Tertua',
            'email' => 'student.oldest@example.test',
            'created_at' => now()->subDays(3),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertViewHas('totalStudents', 3)
            ->assertViewHas('totalTeachers', 2)
            ->assertViewHas('totalClasses', 0)
            ->assertViewHas('totalUsers', 6)
            ->assertSee('Pengguna Terbaru')
            ->assertSee('Siswa Terbaru')
            ->assertSee('student.recent@example.test')
            ->assertSee('Guru')
            ->assertSee('Siswa')
            ->assertSee('Tahun Ajaran 2026/2027')
            ->assertSee('Semester Ganjil')
            ->assertDontSee('student.oldest@example.test');
    }

    public function test_admin_quick_actions_display_a_safe_coming_soon_notice(): void
    {
        $admin = User::factory()->withRole('admin')->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Kelola Master Data (Kelas)')
            ->assertSee('Tambah guru, siswa, mapel & jurusan')
            ->assertSee('Pengaturan Akademik')
            ->assertSee('Data Administrasi')
            ->assertSee('href="'.route('admin.kelas').'"', false)
            ->assertSee('href="#"', false)
            ->assertSee('data-feature-notice="Segera hadir"', false);
    }

    public function test_role_middleware_forbids_access_to_another_roles_dashboard(): void
    {
        $student = User::factory()->withRole('siswa')->create();

        $this->actingAs($student)
            ->get(route('guru.dashboard'))
            ->assertForbidden();

        $this->get(route('siswa.dashboard'))->assertOk();
    }

    public function test_student_dashboard_shows_an_empty_task_state_without_a_continue_action(): void
    {
        $this->withoutVite();

        $student = User::factory()->withRole('siswa')->create();

        $this->actingAs($student)
            ->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertSee('Belum ada tugas')
            ->assertSee('Tugas yang diberikan guru akan muncul di sini.')
            ->assertSee('Tugas saat ini')
            ->assertSeeInOrder(['Dashboard', 'Tahun Ajaran', 'Tugas', 'Chat', 'Pusat Bantuan'])
            ->assertSee('href="'.route('siswa.dashboard').'"', false)
            ->assertSee('aria-current="page"', false)
            ->assertSee('data-feature-notice="Fitur ini sedang dalam tahap pengembangan"', false)
            ->assertSee('href="#"', false)
            ->assertDontSee('Administrasi')
            ->assertDontSee('Team')
            ->assertDontSee('Lanjutkan Tugas');
    }

    public function test_dashboard_dropdowns_explain_features_that_are_not_available_yet(): void
    {
        $this->withoutVite();

        $student = User::factory()->withRole('siswa')->create();

        $this->actingAs($student)
            ->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertSee('Fitur Chat belum tersedia.')
            ->assertSee('Tidak ada notifikasi baru.')
            ->assertSee('Pengaturan Profil')
            ->assertSee('Pengaturan Akun')
            ->assertSee('Pengaturan Preferensi')
            ->assertSee('disabled', false);
    }

    public function test_authenticated_users_are_redirected_to_their_dashboard_from_login(): void
    {
        foreach (self::roleDashboardRoutes() as [$roleName, $dashboardRoute]) {
            $user = User::factory()->withRole($roleName)->create();

            $this->actingAs($user)
                ->get(route('login'))
                ->assertRedirect(route($dashboardRoute));
        }
    }

    public function test_authenticated_users_reach_their_dashboard_from_the_homepage(): void
    {
        foreach (self::roleDashboardRoutes() as [$roleName, $dashboardRoute]) {
            $user = User::factory()->withRole($roleName)->create();

            $this->actingAs($user)
                ->get('/')
                ->assertRedirect(route('login'));

            $this->get(route('login'))
                ->assertRedirect(route($dashboardRoute));
        }
    }

    public function test_dashboard_redirects_guests_to_login(): void
    {
        $this->get(route('siswa.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_login_rejects_invalid_credentials_and_keeps_the_email(): void
    {
        $email = 'siswa@gmail.com';

        $this->from(route('login'))
            ->post(route('login.post'), [
                'email' => $email,
                'password' => 'wrong-password',
                'remember' => '1',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'email' => 'Email atau kata sandi yang Anda masukkan salah.',
            ])
            ->assertSessionHas('_old_input.email', $email)
            ->assertSessionHas('_old_input.remember', '1');

        $this->assertGuest();
    }

    public function test_login_remember_option_sets_a_persistent_token(): void
    {
        $user = User::factory()->withRole('guru')->create([
            'password' => 'secret-password',
            'remember_token' => null,
        ]);

        $this->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'secret-password',
            'remember' => '1',
        ])->assertRedirect(route('guru.dashboard'));

        $this->assertNotNull($user->fresh()->getRememberToken());
    }

    public function test_logout_invalidates_the_authenticated_session(): void
    {
        $user = User::factory()->withRole('siswa')->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_database_uses_role_and_profile_tables_instead_of_user_role_columns(): void
    {
        $this->assertTrue(Schema::hasColumn('users', 'role_id'));
        $this->assertFalse(Schema::hasColumn('users', 'role'));
        $this->assertFalse(Schema::hasColumn('users', 'nisn'));
        $this->assertTrue(Schema::hasTable('roles'));
        $this->assertTrue(Schema::hasTable('guru_profiles'));
        $this->assertTrue(Schema::hasColumn('siswa_profiles', 'nisn'));
    }

    public function test_student_profile_requires_nisn_at_the_database_level(): void
    {
        $student = User::factory()->withRole('siswa')->create();

        $this->expectException(QueryException::class);

        DB::table('siswa_profiles')->insert([
            'user_id' => $student->getKey(),
            'nisn' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_email_is_required_for_every_user(): void
    {
        $roleId = Role::query()->where('name', 'siswa')->value('id');

        $this->expectException(QueryException::class);

        DB::table('users')->insert([
            'name' => 'Student',
            'email' => null,
            'password' => Hash::make('secret-password'),
            'role_id' => $roleId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_normalization_migration_preserves_existing_student_roles_and_nisn(): void
    {
        $migration = require database_path(
            'migrations/2026_11_17_094942_normalize_users_roles_and_create_profiles.php',
        );
        $migration->down();

        DB::table('users')->insert([
            'name' => 'Existing Student',
            'email' => 'existing-student@example.test',
            'password' => Hash::make('secret-password'),
            'role' => 'siswa',
            'nisn' => '0012345678',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $migration->up();

        $user = User::query()->where('email', 'existing-student@example.test')->firstOrFail();

        $this->assertTrue($user->hasRole('siswa'));
        $this->assertDatabaseHas('siswa_profiles', [
            'user_id' => $user->getKey(),
            'nisn' => '0012345678',
        ]);
    }

    public function test_a_role_with_users_cannot_be_deleted(): void
    {
        $user = User::factory()->withRole('guru')->create();
        $role = Role::query()->findOrFail($user->role_id);

        $this->expectException(QueryException::class);

        $role->delete();
    }

    public function test_role_user_seeder_creates_the_three_local_test_accounts(): void
    {
        $this->seed(RoleUserSeeder::class);
        $this->seed(RoleUserSeeder::class);

        $this->assertDatabaseCount('roles', 3);
        $this->assertDatabaseCount('users', 3);
        $this->assertDatabaseHas('users', ['email' => 'admin@gmail.com']);
        $this->assertDatabaseHas('users', ['email' => 'guru@gmail.com']);
        $this->assertDatabaseHas('guru_profiles', [
            'user_id' => User::query()->where('email', 'guru@gmail.com')->value('id'),
            'nip' => '198501012010011001',
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'siswa@gmail.com',
            'role_id' => Role::query()->where('name', 'siswa')->value('id'),
        ]);
        $this->assertDatabaseHas('siswa_profiles', [
            'user_id' => User::query()->where('email', 'siswa@gmail.com')->value('id'),
            'nisn' => '1234567890',
        ]);

        $this->post(route('login.post'), [
            'email' => 'siswa@gmail.com',
            'password' => 'password',
        ])->assertRedirect(route('siswa.dashboard'));
    }

    public function test_database_seeder_adds_master_data_idempotently_without_overwriting_existing_rows(): void
    {
        Jurusan::query()->create([
            'kode_jurusan' => 'PPLG',
            'nama_jurusan' => 'Nama lokal yang sudah ada',
        ]);

        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('jurusans', [
            'kode_jurusan' => 'PPLG',
            'nama_jurusan' => 'Nama lokal yang sudah ada',
        ]);
        $this->assertDatabaseCount('jurusans', 1);
        $this->assertDatabaseCount('mata_pelajarans', 3);
        $this->assertDatabaseHas('mata_pelajarans', [
            'kode_mapel' => 'PWPB',
            'nama_mapel' => 'Pemrograman Web dan Perangkat Bergerak',
        ]);
        $this->assertDatabaseCount('kelas', 1);
        $this->assertDatabaseHas('kelas', [
            'nama_kelas' => 'X PPLG 1',
            'tingkat' => 'X',
            'jurusan_id' => null,
            'wali_kelas_id' => null,
        ]);
    }

    public function test_a_student_can_log_in_with_their_email(): void
    {
        $student = User::factory()->withRole('siswa')->create([
            'password' => 'secret-password',
        ]);
        $student->siswaProfile()->create(['nisn' => '0012345678']);

        $this->post(route('login.post'), [
            'email' => $student->email,
            'password' => 'secret-password',
        ])->assertRedirect(route('siswa.dashboard'));

        $this->assertAuthenticatedAs($student);
    }
}
