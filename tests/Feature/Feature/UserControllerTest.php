<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase; // Refresh the database for each test

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create User
        $user = User::factory()->create();

        // Act as the user
        $this->actingAs($user);
    }

    /**
     * Test fetching a list of users.
     *
     * @return void
     */
    public function test_index()
    {
        // Create users
        User::factory()->count(4)->create();

        // Make a GET request to the index endpoint
        $response = $this->get('/users');

        // Assert that the response status is 200 OK
        $response->assertStatus(200);

        // Assert that the Inertia response contains the users
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/Users/Index')
            ->has('payload.data', 5)
        );
    }

    /**
     * Test creating a new user.
     *
     * @return void
     */
    public function test_store()
    {
        // Make a POST request to the store endpoint
        $response = $this->post('/users', [
            'name' => 'test',
            'surname' => 'test',
            'email' => 'test@gmail.com',
            'password' => '12345678',

        ]);

        // Ensure there are no validation errors
        $response->assertSessionHasNoErrors();

        // Assert that the response status is 302 (redirect)
        $response->assertStatus(302);

        // Assert that the user was created in the database
        $this->assertDatabaseHas('users', [
            'email' => 'test@gmail.com',
        ]);
    }

    /**
     * Test creating a user with invalid data.
     *
     * @return void
     */
    public function test_store_with_invalid_data()
    {
        // Make a POST request to the store endpoint with invalid data
        $response = $this->post('/users', []);

        // Assert that the response status is 302 (redirect with validation errors)
        $response->assertStatus(302);

        // Assert that the session contains validation errors for nested fields
        $response->assertSessionHasErrors([
            'name',
            'surname',
            'email',
        ]);
    }

    /**
     * Test updating an existing user.
     *
     * @return void
     */
    public function test_update()
    {
        $user = User::factory()->create();

        // Make a PUT request to the update endpoint
        $response = $this->put("/users/{$user->id}", [
            'name' => 'test2',
            'surname' => 'test2',
            'email' => 'test2@gmail.com',
            'password' => '12345678',
        ]);

        // Ensure there are no validation errors
        $response->assertSessionHasNoErrors();

        // Assert that the response status is 302 (redirect)
        $response->assertStatus(302);

        // Assert that the user was updated in the database
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'test2',
        ]);
    }

    /**
     * Test deleting a user.
     *
     * @return void
     */
    public function test_destroy()
    {
        $user = User::factory()->create();

        // Make a DELETE request to the destroy endpoint
        $response = $this->delete("/users/{$user->id}");

        // Assert that the response status is 302 (redirect)
        $response->assertStatus(302);

        // Assert that the user was deleted from the database
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
