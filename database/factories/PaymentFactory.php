<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => Student::query()->inRandomOrder()->value('id'),
            'amount' => fake()->randomFloat(2, 2500, 6500),
            'payment_date' => fake()->dateTimeBetween('-8 months', 'now'),
            'due_date' => fake()->dateTimeBetween('-8 months', '+1 month'),
            'method' => fake()->randomElement(['cash', 'gcash', 'bank_transfer']),
            'reference_number' => fake()->unique()->bothify('PAY-######'),
            'status' => fake()->randomElement(['paid', 'pending', 'overdue']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
