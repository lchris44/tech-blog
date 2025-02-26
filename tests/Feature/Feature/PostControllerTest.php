<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase; // Refresh the database for each test

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create User
        $this->user = User::factory()->create();

        // Act as the user
        $this->actingAs($this->user);
    }

    /**
     * Test fetching a list of posts.
     *
     * @return void
     */
    public function test_index()
    {
        // Create posts
        Post::factory()->count(5)->create(['user_id' => $this->user->id]);

        // Make a GET request to the index endpoint
        $response = $this->get('/posts');

        // Assert that the response status is 200 OK
        $response->assertStatus(200);

        // Assert that the Inertia response contains the posts
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/Posts/Index')
            ->has('payload.data', 5)
        );
    }

    /**
     * Test creating a new post.
     *
     * @return void
     */
    public function test_store()
    {
        // Create tags
        $tags = Tag::factory()->count(3)->create();

        // Make a POST request to the store endpoint
        $response = $this->post('/posts', [
            'title' => ['en' => 'Test Title', 'it' => 'Titolo di Test'],
            'content' => ['en' => 'Test Content', 'it' => 'Contenuto di Test'],
            'short_description' => ['en' => 'Test Description', 'it' => 'Descrizione di Test'],
            'tags' => $tags->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                ];
            })->toArray(),
        ]);

        // Assert that the response status is 302 (redirect)
        $response->assertStatus(302);

        // Assert that the post was created in the database
        $this->assertDatabaseHas('posts', [
            'title->en' => 'Test Title',
            'content->en' => 'Test Content',
            'short_description->en' => 'Test Description',
        ]);

        // Assert that the tags were attached to the post
        $post = Post::first();
        $this->assertCount(3, $post->tags);
    }

    /**
     * Test creating a post with invalid data.
     *
     * @return void
     */
    public function test_store_with_invalid_data()
    {
        // Make a POST request to the store endpoint with invalid data
        $response = $this->post('/posts', [
            'title' => [
                'en' => '', // Missing English title
                'it' => '', // Missing Italian title
            ],
            'content' => [
                'en' => '', // Missing English content
                'it' => '', // Missing Italian content
            ],
            'short_description' => [
                'en' => '', // Missing English short description
                'it' => '', // Missing Italian short description
            ],
            'tags' => [], // Missing tags
        ]);

        // Assert that the response status is 302 (redirect with validation errors)
        $response->assertStatus(302);

        // Assert that the session contains validation errors for nested fields
        $response->assertSessionHasErrors([
            'title.en',
            'title.it',
            'content.en',
            'content.it',
            'short_description.en',
            'short_description.it',
            'tags',
        ]);
    }

    /**
     * Test updating an existing post.
     *
     * @return void
     */
    public function test_update()
    {
        // Create post, and tags
        $post = Post::factory()->create(['user_id' => $this->user->id]);
        $tags = Tag::factory()->count(3)->create();

        // Make a PUT request to the update endpoint
        $response = $this->put("/posts/{$post->id}", [
            'title' => ['en' => 'Updated Title', 'it' => 'Titolo Aggiornato'],
            'content' => ['en' => 'Updated Content', 'it' => 'Contenuto Aggiornato'],
            'short_description' => ['en' => 'Updated Description', 'it' => 'Descrizione Aggiornata'],
            'tags' => $tags->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                ];
            })->toArray(),
        ]);

        // Assert that the response status is 302 (redirect)
        $response->assertStatus(302);

        // Assert that the post was updated in the database
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title->en' => 'Updated Title',
            'content->en' => 'Updated Content',
            'short_description->en' => 'Updated Description',
        ]);

        // Assert that the tags were synced to the post
        $this->assertCount(3, $post->fresh()->tags);
    }

    /**
     * Test deleting a post.
     *
     * @return void
     */
    public function test_destroy()
    {
        // Create post
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        // Make a DELETE request to the destroy endpoint
        $response = $this->delete("/posts/{$post->id}");

        // Assert that the response status is 302 (redirect)
        $response->assertStatus(302);

        // Assert that the post was deleted from the database
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    /**
     * Test uploading a cover image.
     *
     * @return void
     */
    public function test_upload_cover()
    {
        // Fake the storage disk
        Storage::fake('public');

        // Create post
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        // Create a fake image file
        $file = UploadedFile::fake()->image('cover.jpg');

        // Make a POST request to the upload cover endpoint
        $response = $this->post("/posts/{$post->id}/upload-cover", [
            'file' => $file,
        ]);

        // Assert that the response status is 200 OK
        $response->assertStatus(200);

        // Assert that the cover image was saved in the database
        $this->assertNotNull($post->fresh()->cover);

        // Assert that the file was stored in the public disk
        Storage::disk('public')->assertExists("uploads/posts/{$post->id}.jpg");
    }

    /**
     * Test removing a cover image.
     *
     * @return void
     */
    public function test_remove_cover()
    {
        // Fake the storage disk
        Storage::fake('public');

        // Create a user and post with a cover image
        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'cover' => '/storage/uploads/posts/1.jpg',
        ]);

        // Make a DELETE request to the remove cover endpoint
        $response = $this->delete("/posts/{$post->id}/remove-cover");

        // Assert that the response status is 302 (redirect)
        $response->assertStatus(302);

        // Refresh the post instance
        $post->refresh();

        // Assert that the cover image was removed from the database
        $this->assertNull($post->cover);

        // Assert that the file was deleted from the public disk
        Storage::disk('public')->assertMissing('uploads/posts/1.jpg');
    }
}
