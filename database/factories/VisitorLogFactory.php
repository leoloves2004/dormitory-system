<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitorLogFactory extends Factory
{
    public function definition(): array
    {
        $timeIn = fake()->dateTimeBetween('-1 month', 'now');

        return [
            'tenant_id' => Tenant::query()->inRandomOrder()->value('id'),
            'visitor_name' => fake()->name(),
            'visitor_phone' => fake()->phoneNumber(),
            'visitor_count' => fake()->numberBetween(1, 8),
            'visit_date' => $timeIn,
            'purpose' => fake()->randomElement(['Family visit', 'Study group', 'Delivery', 'Maintenance']),
            'time_in' => $timeIn,
            'time_out' => fake()->optional()->dateTimeBetween($timeIn, 'now'),
        ];
    }
}
