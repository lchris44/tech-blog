<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run()
    {
        // Create posts with tags
        Post::factory()
            ->count(20)
            ->create()
            ->each(function ($post) {
                $post->tags()->attach(Tag::inRandomOrder()->take(rand(1, 3))->pluck('id')); // Attach 1 to 3 random tags to the post
            });
    }
}
