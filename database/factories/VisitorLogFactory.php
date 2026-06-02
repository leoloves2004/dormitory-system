<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitorLogFactory extends Factory
{
    public function definition(): array
    {
        $timeIn = fake()->dateTimeBetween('-1 month', 'now');

        return [
            'student_id' => Student::query()->inRandomOrder()->value('id'),
            'room_id' => Room::query()->inRandomOrder()->value('id'),
            'visitor_name' => fake()->name(),
            'visitor_phone' => fake()->phoneNumber(),
            'purpose' => fake()->randomElement(['Family visit', 'Study group', 'Delivery', 'Maintenance']),
            'time_in' => $timeIn,
            'time_out' => fake()->optional()->dateTimeBetween($timeIn, 'now'),
        ];
    }
}
