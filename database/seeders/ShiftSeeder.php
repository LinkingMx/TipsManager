<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shifts = [
            [
                'name' => 'Morning Shift',
                'start_hour' => '06:00',
                'end_hour' => '14:00',
            ],
            [
                'name' => 'Afternoon Shift',
                'start_hour' => '14:00',
                'end_hour' => '22:00',
            ],
            [
                'name' => 'Night Shift',
                'start_hour' => '22:00',
                'end_hour' => '06:00',
            ],
            [
                'name' => 'Brunch Shift',
                'start_hour' => '09:00',
                'end_hour' => '17:00',
            ],
            [
                'name' => 'Dinner Shift',
                'start_hour' => '17:00',
                'end_hour' => '01:00',
            ],
            [
                'name' => 'Weekend AM',
                'start_hour' => '07:00',
                'end_hour' => '15:00',
            ],
            [
                'name' => 'Weekend PM',
                'start_hour' => '15:00',
                'end_hour' => '23:00',
            ],
            [
                'name' => 'Split Shift AM',
                'start_hour' => '08:00',
                'end_hour' => '12:00',
            ],
            [
                'name' => 'Split Shift PM',
                'start_hour' => '18:00',
                'end_hour' => '22:00',
            ],
            [
                'name' => 'Early Bird',
                'start_hour' => '05:00',
                'end_hour' => '11:00',
            ],
        ];

        foreach ($shifts as $shiftData) {
            Shift::firstOrCreate(
                ['name' => $shiftData['name']],
                $shiftData
            );
        }

        $this->command->info('Shifts seeded successfully!');
    }
}
