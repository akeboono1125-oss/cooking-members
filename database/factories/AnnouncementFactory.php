<?php

namespace Database\Factories;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->randomElement(['年末年始の休講のお知らせ', '12月の体験レッスンのご案内', '新しい講師が加わりました']),
            'body' => fake()->realText(800),
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
