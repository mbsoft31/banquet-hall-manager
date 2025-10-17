<?php

namespace Mbsoft\BanquetHallManager\Database\Factories;

use Mbsoft\BanquetHallManager\Models\Invoice;
use Mbsoft\BanquetHallManager\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $issueDate = Carbon::instance($this->faker->dateTimeBetween('-1 month', 'now'));
        $dueDate = (clone $issueDate)->addDays((int) config('banquethallmanager.payment_due_days', 30));
        $subtotal = $this->faker->randomFloat(2, 1000, 8000);
        $tax = $this->faker->randomFloat(2, 80, 640);
        $discount = $this->faker->randomFloat(2, 0, 300);

        return [
            'tenant_id' => config('banquethallmanager.current_tenant_id', 1),
            'event_id' => Event::factory(),
            'client_id' => null,
            'invoice_number' => 'INV-' . $this->faker->unique()->numberBetween(1000, 9999),
            'issue_date' => $issueDate,
            'due_date' => $dueDate,
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'discount_amount' => $discount,
            'total_amount' => max(0, $subtotal + $tax - $discount),
            'status' => $this->faker->randomElement(['draft', 'sent', 'pending', 'paid', 'overdue']),
            'notes' => $this->faker->optional()->sentence(),
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

    public function configure(): static
    {
        return $this->afterMaking(function (Invoice $invoice) {
            if (!$invoice->client_id) {
                $event = $invoice->event ?? Event::find($invoice->event_id);
                if ($event) {
                    $invoice->client_id = $event->client_id;
                }
            }
        })->afterCreating(function (Invoice $invoice) {
            if (!$invoice->client_id) {
                $event = $invoice->event ?? Event::find($invoice->event_id);
                if ($event) {
                    $invoice->client_id = $event->client_id;
                    $invoice->save();
                }
            }
        });
    }
}
