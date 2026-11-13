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
            ->assertSee('data-login-path="/login/siswa"', false)
            ->assertSee('data-login-path="/login/guru"', false);
    }

    public function test_a_student_can_open_the_student_login_page(): void
    {
        $this->withoutVite();

        $this->get('/login/siswa')
            ->assertOk()
            ->assertViewIs('pages.auth.login')
            ->assertSee('Akun Siswa')
            ->assertSee('NISN')
            ->assertSee('name="nisn"', false);
    }

    public function test_a_teacher_can_open_the_teacher_login_page(): void
    {
        $this->withoutVite();

        $this->get('/login/guru')
            ->assertOk()
            ->assertViewIs('pages.auth.login')
            ->assertSee('Akun Guru')
            ->assertSee('Email')
            ->assertSee('name="email"', false);
    }

    public function test_an_unknown_login_role_is_not_available(): void
    {
        $this->get('/login/admin')->assertNotFound();
    }
}
