<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'title' => [
                'en' => $this->faker->sentence(5, true),
                'it' => $this->faker->sentence(5, true),
            ],
            'short_description' => [
                'en' => $this->faker->paragraph,
                'it' => $this->faker->paragraph,
            ],
            'content' => [
                'en' => $this->faker->text(200),
                'it' => $this->faker->text(200),
            ],
        ];
    }
}
