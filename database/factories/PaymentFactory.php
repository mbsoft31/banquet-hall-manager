<?php

namespace Mbsoft\BanquetHallManager\Database\Factories;

use Mbsoft\BanquetHallManager\Models\Payment;
use Mbsoft\BanquetHallManager\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'tenant_id' => config('banquethallmanager.current_tenant_id', 1),
            'invoice_id' => Invoice::factory(),
            'amount' => $this->faker->randomFloat(2, 100, 5000),
            'payment_method' => $this->faker->randomElement([
                'credit_card', 
                'bank_transfer', 
                'cash', 
                'check', 
                'paypal'
            ]),
            'payment_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'transaction_id' => 'txn_' . $this->faker->unique()->randomNumber(8),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed', 'cancelled']),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }
}