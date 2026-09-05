<?php

namespace Database\Factories;

use App\Models\ContentItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<ContentItem> */
class ContentItemFactory extends Factory
{
    protected $model = ContentItem::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(5);

        return [
            'type' => 'post',
            'series_id' => null,
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 99999),
            'excerpt' => fake()->sentence(15),
            'cover_image_path' => null,
            'content_blocks' => [
                ['type' => 'rich_text', 'data' => ['body' => '<p>'.fake()->paragraph().'</p>']],
            ],
            'status' => 'published',
            'sort_order' => 0,
            'published_at' => now()->subDay(),
        ];
    }

    public function type(string $type): static
    {
        return $this->state(fn (): array => ['type' => $type]);
    }

    public function course(): static
    {
        return $this->type('course')->state(['cover_image_path' => 'test/course.jpg']);
    }

    public function book(): static
    {
        return $this->type('book')->state(['cover_image_path' => 'test/book.jpg']);
    }

    public function announcement(): static
    {
        return $this->type('announcement');
    }

    public function draft(): static
    {
        return $this->state(['status' => 'draft', 'published_at' => null]);
    }
}
