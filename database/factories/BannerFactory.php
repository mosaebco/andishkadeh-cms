<?php

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Banner> */
class BannerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(5),
            'subtitle' => fake()->sentence(12),
            'image_path' => 'test/banner.jpg',
            'sort_order' => 0,
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addWeek(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }
}
