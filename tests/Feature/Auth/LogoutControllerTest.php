<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutControllerTest extends TestCase
{
    use RefreshDatabase; // Refresh the database for each test

    /**
     * Test successful logout.
     *
     * @return void
     */
    public function test_successful_logout()
    {
        // Create and log in a user
        $user = User::factory()->create();
        $this->actingAs($user);

        // Log out the user
        $response = $this->postJson('/logout');

        // Assert that the response status is 302 (redirect)
        $response->assertStatus(302);

        // Assert that the user is logged out
        $this->assertGuest();
    }
}
