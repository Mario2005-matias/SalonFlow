<?php

namespace Database\Factories;

use App\Models\Reserve;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reserve>
 */
class ReserveFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('+1 day', '+1 week');
        $end   = (clone $start)->modify('+1 hour');

        return [
            'user_id'    => User::factory(),
            'room_id'    => Room::factory(),
            'start_time' => $start,
            'end_time'   => $end,
            'reason'     => fake()->sentence(),
            'status'     => 'approved',
        ];
    }

    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function cancelated(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'cancelated',
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'rejected',
        ]);
    }

    /**
     * Força um intervalo específico de datas.
     */
    public function between(string $start, string $end): static
    {
        return $this->state(fn(array $attributes) => [
            'start_time' => $start,
            'end_time'   => $end,
        ]);
    }
}
