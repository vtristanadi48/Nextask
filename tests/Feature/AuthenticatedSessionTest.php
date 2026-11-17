<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticatedSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_teacher_can_log_in_with_email_and_reach_the_dashboard(): void
    {
        $this->withoutVite();
        $user = User::factory()->create([
            'role' => 'guru',
            'password' => 'secret-password',
        ]);

        $this->post(route('login.store', ['role' => 'guru']), [
            'email' => $user->email,
            'password' => 'secret-password',
        ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasNoErrors();

        $this->assertAuthenticatedAs($user);
        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Nextask sedang dalam pengembangan.');
    }

    public function test_a_student_can_log_in_with_nisn_and_reach_the_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'siswa',
            'nisn' => '0012345678',
            'password' => 'secret-password',
        ]);

        $this->post(route('login.store', ['role' => 'siswa']), [
            'nisn' => '0012345678',
            'password' => 'secret-password',
        ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasNoErrors();

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_rejects_an_account_with_a_different_role(): void
    {
        $user = User::factory()->create([
            'role' => 'siswa',
            'nisn' => '0012345678',
            'password' => 'secret-password',
        ]);

        $this->from(route('login', ['role' => 'guru']))
            ->post(route('login.store', ['role' => 'guru']), [
                'email' => $user->email,
                'password' => 'secret-password',
            ])
            ->assertRedirect(route('login', ['role' => 'guru']))
            ->assertSessionHasErrors('credentials');

        $this->assertGuest();
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        User::factory()->create([
            'role' => 'guru',
            'password' => 'secret-password',
        ]);

        $this->post(route('login.store', ['role' => 'guru']), [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('credentials');

        $this->assertGuest();
    }

    public function test_dashboard_redirects_guests_to_onboarding(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('onboarding'));
    }

    public function test_a_logged_in_user_can_log_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('onboarding'));

        $this->assertGuest();
    }

    public function test_student_login_rejects_an_invalid_nisn(): void
    {
        $this->post(route('login.store', ['role' => 'siswa']), [
            'nisn' => '123',
            'password' => 'secret-password',
        ])->assertSessionHasErrors('nisn');

        $this->assertGuest();
    }
}
