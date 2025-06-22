<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shift>
 */
class ShiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $shifts = [
            ['name' => 'Morning Shift', 'start' => '06:00', 'end' => '14:00'],
            ['name' => 'Afternoon Shift', 'start' => '14:00', 'end' => '22:00'],
            ['name' => 'Night Shift', 'start' => '22:00', 'end' => '06:00'],
            ['name' => 'Early Morning', 'start' => '05:00', 'end' => '13:00'],
            ['name' => 'Late Evening', 'start' => '18:00', 'end' => '02:00'],
            ['name' => 'Double Shift', 'start' => '08:00', 'end' => '20:00'],
            ['name' => 'Weekend AM', 'start' => '07:00', 'end' => '15:00'],
            ['name' => 'Weekend PM', 'start' => '15:00', 'end' => '23:00'],
        ];

        $shift = $this->faker->randomElement($shifts);

        return [
            'name' => $shift['name'],
            'start_hour' => $shift['start'],
            'end_hour' => $shift['end'],
        ];
    }
}
