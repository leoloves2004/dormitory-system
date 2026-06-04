<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::query()->inRandomOrder()->value('id'),
            'amount' => fake()->randomFloat(2, 2500, 6500),
            'payment_date' => fake()->dateTimeBetween('-8 months', 'now'),
            'due_date' => fake()->dateTimeBetween('-8 months', '+1 month'),
            'payment_method' => fake()->randomElement(['cash', 'gcash', 'bank_transfer']),
            'reference_number' => fake()->unique()->bothify('PAY-######'),
            'status' => fake()->randomElement(['paid', 'pending', 'overdue']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
