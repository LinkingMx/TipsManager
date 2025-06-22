<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobPosition>
 */
class JobPositionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $positions = [
            'Software Developer',
            'Project Manager',
            'Business Analyst',
            'Quality Assurance Engineer',
            'DevOps Engineer',
            'UI/UX Designer',
            'Data Analyst',
            'Product Manager',
            'Scrum Master',
            'Technical Lead',
            'Frontend Developer',
            'Backend Developer',
            'Full Stack Developer',
            'Database Administrator',
            'System Administrator',
            'Sales Manager',
            'Marketing Specialist',
            'Human Resources Manager',
            'Financial Analyst',
            'Customer Service Representative',
        ];

        return [
            'name' => fake()->unique()->randomElement($positions),
            'points' => fake()->randomFloat(2, 5.00, 100.00), // Random decimal between 5.00 and 100.00
            'applies_for_tips' => fake()->boolean(80), // 80% chance of being true
        ];
    }
}
