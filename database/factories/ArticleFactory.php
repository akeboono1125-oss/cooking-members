<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'title' => fake()->randomElement(['肉じゃが', 'ハンバーグ', '麻婆豆腐', 'プリン', '食パン', '親子丼', 'グラタン']).'の作り方',
            'body' => fake()->realText(800),
            'image_path' => null,
            'status' => 'published',
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => [
            'published_at' => fake()->dateTimeBetween('+1 day', '+1 month'),
        ]);
    }
}
