<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DailyTip>
 */
class DailyTipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'amount' => fake()->randomFloat(2, 20, 300),
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    /**
     * Indicate the tip is for today
     */
    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => Carbon::today()->format('Y-m-d'),
        ]);
    }

    /**
     * Indicate the tip is for yesterday
     */
    public function yesterday(): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => Carbon::yesterday()->format('Y-m-d'),
        ]);
    }

    /**
     * Indicate this is a high tip day
     */
    public function high(): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => fake()->randomFloat(2, 200, 500),
        ]);
    }

    /**
     * Indicate this is a low tip day
     */
    public function low(): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => fake()->randomFloat(2, 10, 50),
        ]);
    }
}
