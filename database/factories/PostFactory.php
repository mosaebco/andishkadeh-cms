<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Post> */
class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->sentence(5);

        return [
            'series_id' => null,
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 99999),
            'excerpt' => fake()->sentence(15),
            'content_blocks' => [
                ['type' => 'rich_text', 'data' => ['body' => '<p>'.fake()->paragraph().'</p>']],
            ],
            'status' => 'published',
            'sort_order' => 0,
            'published_at' => now()->subDay(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (): array => ['status' => 'draft', 'published_at' => null]);
    }
}
