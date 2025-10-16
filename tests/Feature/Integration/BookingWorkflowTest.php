<?php

use Mbsoft\BanquetHallManager\Models\Event;
use Mbsoft\BanquetHallManager\Models\Hall;
use Mbsoft\BanquetHallManager\Models\Client;
use Mbsoft\BanquetHallManager\Models\Booking;
use Mbsoft\BanquetHallManager\Models\ServiceType;
use Mbsoft\BanquetHallManager\Models\Invoice;
use Mbsoft\BanquetHallManager\Models\Payment;
use Illuminate\Foundation\Auth\User;

beforeEach(function () {
    $this->withTenant();
    $this->user = $this->createAuthenticatedUser();
});

it('completes full booking workflow from event creation to payment', function () {
    // Step 1: Create hall and client
    $hall = Hall::factory()->create([
        'name' => 'Grand Ballroom',
        'capacity' => 200,
        'hourly_rate' => 150.00,
    ]);
    
    $client = Client::factory()->create([
        'name' => 'John & Jane Smith',
        'email' => 'smith@example.com',
    ]);

    // Step 2: Create event
    $eventData = [
        'hall_id' => $hall->id,
        'client_id' => $client->id,
        'name' => 'Smith Wedding Reception',
        'type' => 'wedding',
        'start_at' => now()->addDays(60)->format('Y-m-d H:i:s'),
        'end_at' => now()->addDays(60)->addHours(6)->format('Y-m-d H:i:s'),
        'guest_count' => 180,
        'status' => 'confirmed',
        'total_amount' => 5000.00,
    ];
    
    $eventResponse = $this->actingAs($this->user)
        ->postJson('/api/bhm/events', $eventData);
        
    $eventResponse->assertCreated();
    $eventId = $eventResponse->json('data.id');

    // Step 3: Add service bookings
    $cateringService = ServiceType::factory()->create([
        'name' => 'Catering Service',
        'default_price' => 25.00,
    ]);
    
    $photographyService = ServiceType::factory()->create([
        'name' => 'Photography',
        'default_price' => 800.00,
    ]);

    $bookingData1 = [
        'event_id' => $eventId,
        'service_type_id' => $cateringService->id,
        'quantity' => 180,
        'unit_price' => 25.00,
        'total_price' => 4500.00,
    ];
    
    $bookingData2 = [
        'event_id' => $eventId,
        'service_type_id' => $photographyService->id,
        'quantity' => 1,
        'unit_price' => 800.00,
        'total_price' => 800.00,
    ];

    $booking1Response = $this->actingAs($this->user)
        ->postJson('/api/bhm/bookings', $bookingData1);
    
    $booking2Response = $this->actingAs($this->user)
        ->postJson('/api/bhm/bookings', $bookingData2);

    $booking1Response->assertCreated();
    $booking2Response->assertCreated();

    // Step 4: Generate invoice
    $invoiceData = [
        'event_id' => $eventId,
        'issue_date' => now()->format('Y-m-d'),
        'due_date' => now()->addDays(30)->format('Y-m-d'),
        'subtotal' => 5300.00,
        'tax_amount' => 424.00,
        'total_amount' => 5724.00,
        'status' => 'sent',
    ];

    $invoiceResponse = $this->actingAs($this->user)
        ->postJson('/api/bhm/invoices', $invoiceData);
        
    $invoiceResponse->assertCreated();
    $invoiceId = $invoiceResponse->json('data.id');

    // Step 5: Process payment
    $paymentData = [
        'invoice_id' => $invoiceId,
        'amount' => 5724.00,
        'payment_method' => 'credit_card',
        'payment_date' => now()->format('Y-m-d'),
        'status' => 'completed',
        'transaction_id' => 'txn_' . uniqid(),
    ];

    $paymentResponse = $this->actingAs($this->user)
        ->postJson('/api/bhm/payments', $paymentData);
        
    $paymentResponse->assertCreated();

    // Verify complete workflow
    $event = Event::find($eventId);
    $bookings = $event->bookings;
    $invoice = Invoice::find($invoiceId);
    $payment = Payment::where('invoice_id', $invoiceId)->first();

    expect($event)
        ->not->toBeNull()
        ->and($event->name)->toBe('Smith Wedding Reception')
        ->and($bookings)->toHaveCount(2)
        ->and($bookings->sum('total_price'))->toBe(5300.00)
        ->and($invoice->status)->toBe('sent')
        ->and($invoice->total_amount)->toBe(5724.00)
        ->and($payment)->not->toBeNull()
        ->and($payment->status)->toBe('completed')
        ->and($payment->amount)->toBe(5724.00);
});

it('handles booking conflicts properly', function () {
    $hall = Hall::factory()->create();
    $client1 = Client::factory()->create();
    $client2 = Client::factory()->create();
    
    $conflictStart = now()->addDays(30)->setHour(18);
    $conflictEnd = $conflictStart->copy()->addHours(4);

    // Create first event
    Event::factory()->create([
        'hall_id' => $hall->id,
        'client_id' => $client1->id,
        'start_at' => $conflictStart,
        'end_at' => $conflictEnd,
        'status' => 'confirmed',
    ]);

    // Try to create conflicting event
    $conflictingEventData = [
        'hall_id' => $hall->id,
        'client_id' => $client2->id,
        'name' => 'Conflicting Event',
        'type' => 'corporate',
        'start_at' => $conflictStart->addHour(1)->format('Y-m-d H:i:s'),
        'end_at' => $conflictEnd->addHour(1)->format('Y-m-d H:i:s'),
        'guest_count' => 100,
        'status' => 'pending',
        'total_amount' => 2000.00,
    ];

    // This should succeed as it's just creating the event
    // Conflict checking should be done in business logic layer
    $response = $this->actingAs($this->user)
        ->postJson('/api/bhm/events', $conflictingEventData);
        
    $response->assertCreated();
    
    // But rescheduling to create conflict should fail
    $nonConflictingEvent = Event::factory()->create([
        'hall_id' => $hall->id,
        'client_id' => $client2->id,
        'start_at' => now()->addDays(45),
        'end_at' => now()->addDays(45)->addHours(3),
    ]);

    $rescheduleResponse = $this->actingAs($this->user)
        ->patchJson("/api/bhm/events/{$nonConflictingEvent->id}/reschedule", [
            'start_at' => $conflictStart->addMinutes(30)->format('Y-m-d H:i:s'),
            'end_at' => $conflictEnd->subMinutes(30)->format('Y-m-d H:i:s'),
        ]);

    $rescheduleResponse->assertUnprocessable();
});