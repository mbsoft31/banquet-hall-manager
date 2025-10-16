<?php

namespace Mbsoft\BanquetHallManager\Database\Factories;

use Mbsoft\BanquetHallManager\Models\ServiceType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceTypeFactory extends Factory
{
    protected $model = ServiceType::class;

    public function definition(): array
    {
        return [
            'tenant_id' => config('banquethallmanager.current_tenant_id', 1),
            'name' => $this->faker->randomElement([
                'Catering Service',
                'Photography',
                'Live Music',
                'Decoration',
                'Security',
                'Parking',
                'Audio/Visual Equipment'
            ]),
            'description' => $this->faker->sentence(),
            'default_price' => $this->faker->randomFloat(2, 50, 500),
        ];
    }
}