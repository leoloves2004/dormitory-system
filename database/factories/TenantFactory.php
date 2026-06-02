<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => Student::query()->inRandomOrder()->value('id'),
            'room_id' => Room::query()->inRandomOrder()->value('id'),
            'move_in_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'move_out_date' => null,
            'status' => 'active',
            'remarks' => fake()->optional()->sentence(),
        ];
    }
}
