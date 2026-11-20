<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
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
            'admin' => ['admin', 'admin.dashboard', 'guru', 'email'],
            'guru' => ['guru', 'guru.dashboard', 'guru', 'email'],
            'siswa' => ['siswa', 'siswa.dashboard', 'siswa', 'nisn'],
        ];
    }

    #[DataProvider('roleDashboardRoutes')]
    public function test_a_user_is_redirected_to_the_dashboard_for_their_role(
        string $roleName,
        string $dashboardRoute,
        string $loginRole,
        string $identifierField,
    ): void {
        $user = User::factory()->withRole($roleName)->create([
            'password' => 'secret-password',
        ]);

        if ($roleName === 'siswa') {
            $user->siswaProfile()->create(['nisn' => '0012345678']);
            $identifier = '0012345678';
        } else {
            $identifier = $user->email;
        }

        $this->post(route('login.post'), [
            'login_role' => $loginRole,
            $identifierField => $identifier,
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

        if ($roleName === 'admin') {
            $dashboard->assertSeeInOrder([
                'Dashboard',
                'Administrasi Akademik',
                'Kelas',
                'Chat',
                'Pusat Bantuan',
            ]);
        } else {
            $dashboard->assertSeeInOrder(['Dashboard', 'Kelas', 'Chat', 'Pusat Bantuan'])
                ->assertDontSee('Administrasi Akademik');
        }
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
            ->assertSeeInOrder(['Dashboard', 'Kelas', 'Chat', 'Pusat Bantuan'])
            ->assertSee('href="'.route('siswa.dashboard').'"', false)
            ->assertSee('aria-current="page"', false)
            ->assertSee('aria-disabled="true"', false)
            ->assertDontSee('Administrasi Akademik')
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

    public function test_dashboard_redirects_guests_to_login(): void
    {
        $this->get(route('siswa.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_login_rejects_invalid_credentials_and_keeps_the_identifier(): void
    {
        $this->from(route('login', ['role' => 'siswa']))
            ->post(route('login.post'), [
                'login_role' => 'siswa',
                'nisn' => '0012345678',
                'password' => 'wrong-password',
            ])
            ->assertRedirect(route('login', ['role' => 'siswa']))
            ->assertSessionHasErrors('nisn')
            ->assertSessionHas('_old_input.login_role', 'siswa')
            ->assertSessionHas('_old_input.nisn', '0012345678');

        $this->assertGuest();
    }

    public function test_login_remember_option_sets_a_persistent_token(): void
    {
        $user = User::factory()->withRole('guru')->create([
            'password' => 'secret-password',
            'remember_token' => null,
        ]);

        $this->post(route('login.post'), [
            'login_role' => 'guru',
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
        $this->assertDatabaseHas('users', [
            'email' => 'siswa@nextask.test',
            'role_id' => Role::query()->where('name', 'siswa')->value('id'),
        ]);
        $this->assertDatabaseHas('siswa_profiles', [
            'user_id' => User::query()->where('email', 'siswa@nextask.test')->value('id'),
            'nisn' => '1234567890',
        ]);

        $this->post(route('login.post'), [
            'login_role' => 'siswa',
            'nisn' => '1234567890',
            'password' => 'password',
        ])->assertRedirect(route('siswa.dashboard'));
    }

    public function test_a_student_cannot_log_in_with_their_email(): void
    {
        $student = User::factory()->withRole('siswa')->create([
            'password' => 'secret-password',
        ]);
        $student->siswaProfile()->create(['nisn' => '0012345678']);

        $this->post(route('login.post'), [
            'login_role' => 'guru',
            'email' => $student->email,
            'password' => 'secret-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_a_teacher_cannot_log_in_with_a_student_nisn(): void
    {
        $teacher = User::factory()->withRole('guru')->create([
            'password' => 'secret-password',
        ]);
        $teacher->siswaProfile()->create(['nisn' => '0012345678']);

        $this->post(route('login.post'), [
            'login_role' => 'siswa',
            'nisn' => '0012345678',
            'password' => 'secret-password',
        ])->assertSessionHasErrors('nisn');

        $this->assertGuest();
    }
}
