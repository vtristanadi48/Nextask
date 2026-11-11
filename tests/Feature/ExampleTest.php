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
}
