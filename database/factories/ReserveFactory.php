<?php

namespace Database\Factories;

use App\Models\Reserve;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reserve>
 */
class ReserveFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room_id' => fake()->numberBetween(1, 3),
            'start_time' => fake()->dateTimeBetween('-1 week', '+1 week'),
            'end_time' => fake()->dateTimeBetween('+1 week', '+2 weeks'),
            'reason' => fake()->sentence(),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected', 'cancelated']),
        ];
    }
}
