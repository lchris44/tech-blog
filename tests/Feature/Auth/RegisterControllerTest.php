<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase; // Refresh the database for each test

    /**
     * Test successful registration.
     *
     * @return void
     */
    public function test_successful_registration()
    {
        $response = $this->postJson('/register', [
            'name' => 'John',
            'surname' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        // Assert that the response status is 302 (redirect)
        $response->assertStatus(302);

        // Assert that the user was created in the database
        $this->assertDatabaseHas('users', [
            'name' => 'John',
            'surname' => 'Doe',
            'email' => 'john.doe@example.com',
        ]);

        // Assert that the user is logged in
        $this->assertAuthenticated();
    }

    /**
     * Test registration with invalid data.
     *
     * @return void
     */
    public function test_registration_with_invalid_data()
    {
        $response = $this->postJson('/register', [
            'name' => '', // Missing name
            'surname' => '', // Missing surname
            'email' => 'invalid-email', // Invalid email
            'password' => 'weak', // Weak password
            'password_confirmation' => 'weak', // Mismatched password
        ]);

        // Assert that the response status is 422 (validation error)
        $response->assertStatus(422);

        // Assert that the response contains validation errors
        $response->assertJsonValidationErrors(['name', 'surname', 'email', 'password']);
    }
}
