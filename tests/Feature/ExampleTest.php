<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->withoutVite();

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_the_onboarding_page_offers_student_and_teacher_roles(): void
    {
        $this->withoutVite();

        $response = $this->get('/');

        $response
            ->assertViewIs('pages.onboarding')
            ->assertSee('Siswa')
            ->assertSee('Guru')
            ->assertSee('action="'.route('login').'"', false)
            ->assertSee('method="GET"', false)
            ->assertSee('name="role"', false)
            ->assertSee('required', false)
            ->assertSee('Pilih peran Siswa atau Guru terlebih dahulu.')
            ->assertSee('Mulai Sekarang')
            ->assertDontSee('disabled>Mulai Sekarang', false);
    }

    public function test_a_user_can_open_the_login_form(): void
    {
        $this->withoutVite();

        $this->get(route('login'))
            ->assertOk()
            ->assertViewIs('auth.login')
            ->assertSee('Akun Nextask')
            ->assertSee('Masukkan email dan kata sandi untuk mengakses akun Nextask Anda.')
            ->assertSee('name="email"', false)
            ->assertSee('placeholder="nama@gmail.com"', false)
            ->assertSee('name="remember"', false)
            ->assertSee('method="POST"', false)
            ->assertSee('action="'.route('login.post').'"', false)
            ->assertSee('Masuk ke Akun')
            ->assertDontSee('name="nisn"', false)
            ->assertDontSee('name="login_role"', false);
    }

    public function test_login_form_is_the_same_for_every_selected_role(): void
    {
        $this->withoutVite();

        foreach (['siswa', 'guru', 'admin'] as $role) {
            $this->get(route('login', ['role' => $role]))
                ->assertOk()
                ->assertSee('Akun Nextask')
                ->assertSee('name="email"', false)
                ->assertDontSee('name="nisn"', false)
                ->assertDontSee('name="login_role"', false);
        }
    }

    public function test_an_invalid_role_query_still_uses_the_shared_email_form(): void
    {
        $this->withoutVite();

        $this->get('/login?role%5B%5D=siswa')
            ->assertOk()
            ->assertSee('Akun Nextask')
            ->assertSee('name="email"', false)
            ->assertDontSee('name="nisn"', false)
            ->assertDontSee('name="login_role"', false);
    }

    public function test_a_role_specific_login_url_is_not_available(): void
    {
        $this->get('/login/siswa')->assertNotFound();
    }
}
