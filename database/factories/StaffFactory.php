<?php

namespace Mbsoft\BanquetHallManager\Database\Factories;

use Mbsoft\BanquetHallManager\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

class StaffFactory extends Factory
{
    protected $model = Staff::class;

    public function definition(): array
    {
        return [
            'tenant_id' => config('banquethallmanager.current_tenant_id', 1),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'position' => $this->faker->randomElement([
                'Event Coordinator',
                'Server',
                'Bartender',
                'Security Guard',
                'Technician',
                'Manager'
            ]),
            'hourly_rate' => $this->faker->randomFloat(2, 15, 50),
        ];
    }
}