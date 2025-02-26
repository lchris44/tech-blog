<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TagControllerTest extends TestCase
{
    use RefreshDatabase; // Refresh the database for each test

    protected function setUp(): void
    {
        parent::setUp();

        // Create User
        $user = User::factory()->create();

        // Act as the user
        $this->actingAs($user);
    }

    /**
     * Test fetching a list of tags.
     *
     * @return void
     */
    public function test_index()
    {
        // Create a user and tags
        Tag::factory()->count(5)->create();

        // Make a GET request to the index endpoint
        $response = $this->get('/tags');

        // Assert that the response status is 200 OK
        $response->assertStatus(200);

        // Assert that the Inertia response contains the tags
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/Tags/Index')
            ->has('payload.data', 5)
        );
    }

    /**
     * Test creating a new tag.
     *
     * @return void
     */
    public function test_store()
    {
        // Create tags
        $tags = Tag::factory()->count(3)->create();

        // Make a POST request to the store endpoint
        $response = $this->post('/tags', [
            'name' => ['en' => 'Test Title', 'it' => 'Titolo di Test'],
        ]);

        // Assert that the response status is 302 (redirect)
        $response->assertStatus(302);

        // Assert that the tag was created in the database
        $this->assertDatabaseHas('tags', [
            'name->en' => 'Test Title',
        ]);
    }

    /**
     * Test creating a tag with invalid data.
     *
     * @return void
     */
    public function test_store_with_invalid_data()
    {
        // Make a POST request to the store endpoint with invalid data
        $response = $this->post('/tags', [
            'name' => [
                'en' => '', // Missing English name
                'it' => '', // Missing Italian name
            ],
        ]);

        // Assert that the response status is 302 (redirect with validation errors)
        $response->assertStatus(302);

        // Assert that the session contains validation errors for nested fields
        $response->assertSessionHasErrors([
            'name.en',
            'name.it',
        ]);
    }

    /**
     * Test updating an existing tag.
     *
     * @return void
     */
    public function test_update()
    {
        // Create a tag, and tags
        $tag = Tag::factory()->create();

        // Make a PUT request to the update endpoint
        $response = $this->put("/tags/{$tag->id}", [
            'name' => ['en' => 'Updated Title', 'it' => 'Titolo Aggiornato'],
        ]);

        // Assert that the response status is 302 (redirect)
        $response->assertStatus(302);

        // Assert that the tag was updated in the database
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name->en' => 'Updated Title',
        ]);
    }

    /**
     * Test deleting a tag.
     *
     * @return void
     */
    public function test_destroy()
    {
        // Create a tag
        $tag = Tag::factory()->create();

        // Make a DELETE request to the destroy endpoint
        $response = $this->delete("/tags/{$tag->id}");

        // Assert that the response status is 302 (redirect)
        $response->assertStatus(302);

        // Assert that the tag was deleted from the database
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }
}
