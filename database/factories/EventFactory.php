<?php

namespace Mbsoft\BanquetHallManager\Database\Factories;

use Mbsoft\BanquetHallManager\Models\Event;
use Mbsoft\BanquetHallManager\Models\Hall;
use Mbsoft\BanquetHallManager\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $startAt = $this->faker->dateTimeBetween('+1 week', '+3 months');
        $endAt = (clone $startAt)->modify('+' . $this->faker->numberBetween(2, 8) . ' hours');

        return [
            'tenant_id' => config('banquethallmanager.current_tenant_id', 1),
            'hall_id' => Hall::factory(),
            'client_id' => Client::factory(),
            'name' => $this->faker->words(3, true),
            'type' => $this->faker->randomElement(['wedding', 'conference', 'birthday', 'corporate', 'graduation']),
            'start_at' => $startAt,
            'end_at' => $endAt,
            'guest_count' => $this->faker->numberBetween(20, 300),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
            'special_requests' => $this->faker->optional()->randomElements(
                ['vegetarian_menu', 'live_music', 'photography', 'decorations', 'parking'],
                $this->faker->numberBetween(0, 3)
            ),
            'total_amount' => $this->faker->randomFloat(2, 500, 10000),
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    public function wedding(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'wedding',
            'guest_count' => $this->faker->numberBetween(80, 300),
            'total_amount' => $this->faker->randomFloat(2, 3000, 15000),
        ]);
    }

    public function conference(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'conference',
            'guest_count' => $this->faker->numberBetween(50, 200),
            'special_requests' => ['projector', 'microphone', 'wifi'],
        ]);
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Event $event) {
            if (!$event->tenant_id) {
                $event->tenant_id = config('banquethallmanager.current_tenant_id', config('banquethallmanager.default_tenant_id', 1));
            }
        })->afterCreating(function (Event $event) {
            if (!$event->tenant_id) {
                $event->tenant_id = config('banquethallmanager.current_tenant_id', config('banquethallmanager.default_tenant_id', 1));
                $event->save();
            }
        });
    }
}
