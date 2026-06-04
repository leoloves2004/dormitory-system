<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'room_number' => fake()->unique()->bothify('R-###'),
            'room_type' => fake()->randomElement(['single', 'double', 'shared']),
            'building' => fake()->randomElement(['North Hall', 'South Hall', 'East Wing']),
            'floor' => fake()->numberBetween(1, 5),
            'capacity' => fake()->randomElement([2, 4, 6]),
            'occupied_slots' => 0,
            'monthly_fee' => fake()->randomFloat(2, 2500, 6500),
            'status' => 'available',
            'amenities' => fake()->randomElement(['WiFi, Study Desk, Cabinet', 'WiFi, Air Conditioning', 'Fan, Cabinet, Shared Bath']),
            'qr_code' => fake()->uuid(),
        ];
    }
}
