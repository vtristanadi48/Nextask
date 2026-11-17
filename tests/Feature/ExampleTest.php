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
            ->assertSee('Akun Guru & Admin')
            ->assertSee('name="email"', false)
            ->assertSee('name="remember"', false)
            ->assertSee('method="POST"', false)
            ->assertSee('action="'.route('login.post').'"', false)
            ->assertSee('Masuk ke Akun');
    }

    public function test_the_student_login_form_uses_a_nisn_field(): void
    {
        $this->withoutVite();

        $this->get(route('login', ['role' => 'siswa']))
            ->assertOk()
            ->assertSee('Akun Siswa')
            ->assertSee('Masukkan NISN dan kata sandi untuk mulai belajar dan mengerjakan tugas.')
            ->assertSee('name="login_role" value="siswa"', false)
            ->assertSee('name="nisn"', false)
            ->assertSee('inputmode="numeric"', false)
            ->assertDontSee('name="email"', false);
    }

    public function test_the_teacher_login_form_uses_an_email_field(): void
    {
        $this->withoutVite();

        $this->get(route('login', ['role' => 'guru']))
            ->assertOk()
            ->assertSee('Akun Guru & Admin')
            ->assertSee('Masukkan email dan kata sandi untuk mengelola kelas dan tugas.')
            ->assertSee('name="login_role" value="guru"', false)
            ->assertSee('name="email"', false)
            ->assertDontSee('name="nisn"', false);
    }

    public function test_an_invalid_role_query_uses_the_default_email_form(): void
    {
        $this->withoutVite();

        $this->get('/login?role%5B%5D=siswa')
            ->assertOk()
            ->assertSee('name="email"', false)
            ->assertDontSee('name="nisn"', false);
    }

    public function test_a_role_specific_login_url_is_not_available(): void
    {
        $this->get('/login/siswa')->assertNotFound();
    }
}
