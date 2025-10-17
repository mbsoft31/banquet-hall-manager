<?php

use Mbsoft\BanquetHallManager\Models\Event;
use Mbsoft\BanquetHallManager\Models\Hall;
use Mbsoft\BanquetHallManager\Models\Client;
use Mbsoft\BanquetHallManager\Models\Booking;
use Mbsoft\BanquetHallManager\Models\Staff;

beforeEach(function () {
    $this->withTenant();
});

it('can create an event', function () {
    $hall = Hall::factory()->create();
    $client = Client::factory()->create();
    
    $event = Event::factory()->create([
        'hall_id' => $hall->id,
        'client_id' => $client->id,
        'name' => 'Wedding Reception',
        'type' => 'wedding',
        'status' => 'confirmed',
        'guest_count' => 150,
        'total_amount' => 5000.00,
    ]);

    expect($event)
        ->toBeInstanceOf(Event::class)
        ->and($event->name)->toBe('Wedding Reception')
        ->and($event->type)->toBe('wedding')
        ->and($event->status)->toBe('confirmed')
        ->and($event->guest_count)->toBe(150)
        ->and($event->total_amount)->toBe(5000.00)
        ->and($event->hall_id)->toBe($hall->id)
        ->and($event->client_id)->toBe($client->id);
});

it('has correct fillable attributes', function () {
    $event = new Event();
    
    expect($event->getFillable())->toContain(
        'tenant_id',
        'hall_id',
        'client_id',
        'name',
        'type',
        'start_at',
        'end_at',
        'guest_count',
        'status',
        'special_requests',
        'total_amount'
    );
});

it('casts attributes correctly', function () {
    $event = Event::factory()->create([
        'start_at' => '2024-12-25 18:00:00',
        'end_at' => '2024-12-25 23:00:00',
        'special_requests' => ['vegetarian_menu', 'live_music'],
        'total_amount' => 1500.50,
    ]);

    $event = $event->refresh();

    expect($event->start_at)->toBeInstanceOf(Carbon\Carbon::class)
        ->and($event->end_at)->toBeInstanceOf(Carbon\Carbon::class)
        ->and($event->special_requests)->toBeArray()
        ->and($event->special_requests)->toBe(['vegetarian_menu', 'live_music'])
        ->and($event->total_amount)->toBeFloat()
        ->and($event->total_amount)->toBe(1500.50);
});

it('belongs to a hall', function () {
    $hall = Hall::factory()->create();
    $event = Event::factory()->create(['hall_id' => $hall->id]);

    expect($event->hall)
        ->toBeInstanceOf(Hall::class)
        ->and($event->hall->id)->toBe($hall->id);
});

it('belongs to a client', function () {
    $client = Client::factory()->create();
    $event = Event::factory()->create(['client_id' => $client->id]);

    expect($event->client)
        ->toBeInstanceOf(Client::class)
        ->and($event->client->id)->toBe($client->id);
});

it('has many bookings', function () {
    $event = Event::factory()->create();
    $booking1 = Booking::factory()->create(['event_id' => $event->id]);
    $booking2 = Booking::factory()->create(['event_id' => $event->id]);

    expect($event->bookings)
        ->toHaveCount(2)
        ->and($event->bookings->pluck('id')->toArray())->toContain($booking1->id, $booking2->id);
});

it('belongs to many staff members', function () {
    $event = Event::factory()->create();
    $staff1 = Staff::factory()->create();
    $staff2 = Staff::factory()->create();
    
    $event->staff()->attach([$staff1->id, $staff2->id]);

    expect($event->staff)
        ->toHaveCount(2)
        ->and($event->staff->pluck('id')->toArray())->toContain($staff1->id, $staff2->id);
});

it('uses correct table name', function () {
    $event = new Event();
    expect($event->getTable())->toBe('bhm_events');
});

it('has tenant scope applied', function () {
    // Create events with different tenant IDs
    Event::factory()->create(['tenant_id' => 1, 'name' => 'Tenant 1 Event']);
    Event::factory()->create(['tenant_id' => 2, 'name' => 'Tenant 2 Event']);
    
    // Set current tenant to 1
    $this->withTenant(1);
    
    $events = Event::all();
    
    expect($events)
        ->toHaveCount(1)
        ->and($events->first()->name)->toBe('Tenant 1 Event')
        ->and($events->first()->tenant_id)->toBe(1);
});