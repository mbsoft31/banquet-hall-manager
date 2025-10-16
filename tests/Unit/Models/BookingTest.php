<?php

use Mbsoft\BanquetHallManager\Models\Booking;
use Mbsoft\BanquetHallManager\Models\Event;
use Mbsoft\BanquetHallManager\Models\ServiceType;

beforeEach(function () {
    $this->withTenant();
});

it('can create a booking', function () {
    $event = Event::factory()->create();
    $serviceType = ServiceType::factory()->create();
    
    $booking = Booking::factory()->create([
        'event_id' => $event->id,
        'service_type_id' => $serviceType->id,
        'quantity' => 5,
        'unit_price' => 50.00,
        'total_price' => 250.00,
    ]);

    expect($booking)
        ->toBeInstanceOf(Booking::class)
        ->and($booking->event_id)->toBe($event->id)
        ->and($booking->service_type_id)->toBe($serviceType->id)
        ->and($booking->quantity)->toBe(5)
        ->and($booking->unit_price)->toBe(50.00)
        ->and($booking->total_price)->toBe(250.00);
});

it('belongs to an event', function () {
    $event = Event::factory()->create();
    $booking = Booking::factory()->create(['event_id' => $event->id]);

    expect($booking->event)
        ->toBeInstanceOf(Event::class)
        ->and($booking->event->id)->toBe($event->id);
});

it('belongs to a service type', function () {
    $serviceType = ServiceType::factory()->create();
    $booking = Booking::factory()->create(['service_type_id' => $serviceType->id]);

    expect($booking->serviceType)
        ->toBeInstanceOf(ServiceType::class)
        ->and($booking->serviceType->id)->toBe($serviceType->id);
});

it('uses correct table name', function () {
    $booking = new Booking();
    expect($booking->getTable())->toBe('bhm_bookings');
});

it('casts prices correctly', function () {
    $booking = Booking::factory()->create([
        'unit_price' => 75.50,
        'total_price' => 225.00,
    ]);

    expect($booking->unit_price)
        ->toBeFloat()
        ->and($booking->unit_price)->toBe(75.50)
        ->and($booking->total_price)->toBeFloat()
        ->and($booking->total_price)->toBe(225.00);
});