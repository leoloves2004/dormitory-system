<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => Student::query()->inRandomOrder()->value('id'),
            'preferred_room_id' => Room::query()->inRandomOrder()->value('id'),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'preferred_move_in_date' => fake()->dateTimeBetween('now', '+2 months'),
            'reason' => fake()->sentence(12),
        ];
    }
}
