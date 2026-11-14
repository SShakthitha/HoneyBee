<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0771234567',
            'address' => '123 Main Street',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('customers', [
            'email' => 'test@example.com',
            'phone' => '0771234567',
            'address' => '123 Main Street',
        ]);
        $response->assertRedirect(route('customer.dashboard', absolute: false));
    }

    public function test_new_registration_requires_phone_and_address(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'Test User',
            'email' => 'missing-contact@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/register')
            ->assertSessionHasErrors(['phone', 'address']);
        $this->assertDatabaseMissing('customers', ['email' => 'missing-contact@example.com']);
    }
}
