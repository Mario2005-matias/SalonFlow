<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'         => fake()->unique()->sentence(3),
            'description'  => fake()->paragraph(),
            'capacity'     => fake()->numberBetween(1, 100),
            'location'     => fake()->address(),
            'is_available' => true,
            'category_id'  => Category::factory(),
        ];
    }

    public function unavailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => false,
        ]);
    }
}
