<?php

namespace Tests\Feature\Api\v1;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase; // Refresh the database for each test

    /**
     * Test fetching all posts.
     *
     * @return void
     */
    public function test_fetch_all_posts()
    {
        // Create 15 posts in the database
        Post::factory()->count(15)->create();

        // Make a GET request to the posts endpoint
        $response = $this->getJson('/api/v1/posts');

        // Assert that the response status is 200 OK
        $response->assertStatus(200);

        // Assert that the response contains 10 posts (due to pagination)
        $response->assertJsonCount(10, 'data');

        // Assert that the response has pagination metadata
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'short_description', 'content', 'cover', 'tags', 'created_at', 'updated_at'],
            ],
            'links',
            'meta',
        ]);
    }

    /**
     * Test filtering posts by tags.
     *
     * @return void
     */
    public function test_filter_posts_by_tags()
    {
        // Create tags
        $tag1 = Tag::factory()->create(['name' => ['en' => 'AI', 'it' => 'Intelligenza Artificiale']]);
        $tag2 = Tag::factory()->create(['name' => ['en' => 'Tech', 'it' => 'Tecnologia']]);

        // Create posts and associate them with tags
        $post1 = Post::factory()->create();
        $post1->tags()->attach($tag1);

        $post2 = Post::factory()->create();
        $post2->tags()->attach($tag2);

        // Make a GET request to filter posts by tags
        $response = $this->getJson('/api/v1/posts?tags=AI,Tech');

        // Assert that the response status is 200 OK
        $response->assertStatus(200);

        // Assert that the response contains the filtered posts
        $response->assertJsonCount(2, 'data');
    }

    /**
     * Test pagination.
     *
     * @return void
     */
    public function test_pagination()
    {
        // Create 15 posts in the database
        Post::factory()->count(15)->create();

        // Make a GET request to the second page
        $response = $this->getJson('/api/v1/posts?page=2');

        // Assert that the response status is 200 OK
        $response->assertStatus(200);

        // Assert that the response contains 5 posts (since 15 total posts with 10 per page)
        $response->assertJsonCount(5, 'data');
    }

    /**
     * Test rate limiting.
     *
     * @return void
     */
    public function test_rate_limiting()
    {
        // Make 60 requests to the posts endpoint
        for ($i = 0; $i < 60; $i++) {
            $this->getJson('/api/v1/posts');
        }

        // Make the 61st request
        $response = $this->getJson('/api/v1/posts');

        // Assert that the response status is 429 Too Many Requests
        $response->assertStatus(429);

        // Assert that the response contains the rate limit error message
        $response->assertJsonPath('message', 'Too Many Attempts.');
    }

    /**
     * Test caching.
     *
     * @return void
     */
    public function test_caching()
    {
        // Create 10 posts in the database
        Post::factory()->count(10)->create();

        // Make a GET request to the posts endpoint
        $response1 = $this->getJson('/api/v1/posts');

        // Assert that the response status is 200 OK
        $response1->assertStatus(200);

        // Clear the cache and make another request
        Cache::flush();
        $response2 = $this->getJson('/api/v1/posts');

        // Assert that the response status is 200 OK
        $response2->assertStatus(200);

        // Assert that the responses are identical (caching works)
        $this->assertEquals($response1->json(), $response2->json());
    }
}
