<?php

namespace Mbsoft\BanquetHallManager\Database\Factories;

use Mbsoft\BanquetHallManager\Models\Invoice;
use Mbsoft\BanquetHallManager\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'tenant_id' => config('banquethallmanager.current_tenant_id', 1),
            'event_id' => Event::factory(),
            'invoice_number' => 'INV-' . $this->faker->unique()->numberBetween(1000, 9999),
            'issue_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'subtotal' => $this->faker->randomFloat(2, 1000, 8000),
            'tax_amount' => $this->faker->randomFloat(2, 80, 640),
            'total_amount' => function (array $attributes) {
                return $attributes['subtotal'] + $attributes['tax_amount'];
            },
            'status' => $this->faker->randomElement(['draft', 'sent', 'pending', 'paid', 'overdue']),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'overdue',
            'due_date' => $this->faker->dateTimeBetween('-1 month', '-1 day'),
        ]);
    }
}