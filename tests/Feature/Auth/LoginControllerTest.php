<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase; // Refresh the database for each test

    /**
     * Test successful login.
     *
     * @return void
     */
    public function test_successful_login()
    {
        // Create a user
        $user = User::factory()->create([
            'email' => 'john.doe@example.com',
            'password' => bcrypt('Password123!'),
        ]);

        // Attempt to log in
        $response = $this->postJson('/login', [
            'email' => 'john.doe@example.com',
            'password' => 'Password123!',
        ]);

        // Assert that the response status is 302 (redirect)
        $response->assertStatus(302);

        // Assert that the user is logged in
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test login with invalid credentials.
     *
     * @return void
     */
    public function test_login_with_invalid_credentials()
    {
        // Create a user
        User::factory()->create([
            'email' => 'john.doe@example.com',
            'password' => bcrypt('Password123!'),
        ]);

        // Attempt to log in with invalid credentials
        $response = $this->postJson('/login', [
            'email' => 'john.doe@example.com',
            'password' => 'WrongPassword',
        ]);

        // Assert that the response status is 422 (validation error)
        $response->assertStatus(422);

        // Assert that the response contains an error message
        $response->assertJsonPath('message', 'These credentials do not match our records.');
    }

    /**
     * Test login with missing fields.
     *
     * @return void
     */
    public function test_login_with_missing_fields()
    {
        $response = $this->postJson('/login', [
            'email' => '', // Missing email
            'password' => '', // Missing password
        ]);

        // Assert that the response status is 422 (validation error)
        $response->assertStatus(422);

        // Assert that the response contains validation errors
        $response->assertJsonValidationErrors(['email', 'password']);
    }
}
