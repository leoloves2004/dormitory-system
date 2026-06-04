<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'room_id' => Room::query()->inRandomOrder()->value('id'),
            'student_number' => fake()->unique()->numerify('STU-20##-####'),
            'course' => fake()->randomElement(['BSIT', 'BSCS', 'BSA', 'BSED', 'BSBA']),
            'year_level' => fake()->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year']),
            'contact_number' => fake()->phoneNumber(),
            'birthdate' => fake()->dateTimeBetween('-24 years', '-17 years'),
            'gender' => fake()->randomElement(['Female', 'Male', 'Prefer not to say']),
            'address' => fake()->address(),
            'guardian_name' => fake()->name(),
            'guardian_phone' => fake()->phoneNumber(),
            'status' => 'active',
        ];
    }
}
