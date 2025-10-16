<?php

namespace Mbsoft\BanquetHallManager\Database\Factories;

use Mbsoft\BanquetHallManager\Models\Hall;
use Illuminate\Database\Eloquent\Factories\Factory;

class HallFactory extends Factory
{
    protected $model = Hall::class;

    public function definition(): array
    {
        return [
            'tenant_id' => config('banquethallmanager.current_tenant_id', 1),
            'name' => $this->faker->words(2, true) . ' Hall',
            'description' => $this->faker->paragraph(),
            'capacity' => $this->faker->numberBetween(50, 500),
            'hourly_rate' => $this->faker->randomFloat(2, 100, 1000),
        ];
    }

    public function withCapacity(int $capacity): static
    {
        return $this->state(fn (array $attributes) => [
            'capacity' => $capacity,
        ]);
    }

    public function withRate(float $rate): static
    {
        return $this->state(fn (array $attributes) => [
            'hourly_rate' => $rate,
        ]);
    }
}