<?php

namespace Mbsoft\BanquetHallManager\Database\Factories;

use Mbsoft\BanquetHallManager\Models\Booking;
use Mbsoft\BanquetHallManager\Models\Event;
use Mbsoft\BanquetHallManager\Models\ServiceType;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 10);
        $unitPrice = $this->faker->randomFloat(2, 25, 200);
        $totalPrice = $quantity * $unitPrice;

        return [
            'tenant_id' => config('banquethallmanager.current_tenant_id', 1),
            'event_id' => Event::factory(),
            'service_type_id' => ServiceType::factory(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}