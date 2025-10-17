<?php

// Basic smoke tests to ensure the testing environment works

use Mbsoft\BanquetHallManager\Models\Event;
use Mbsoft\BanquetHallManager\Models\Hall;
use Mbsoft\BanquetHallManager\Models\Client;

beforeEach(function () {
    $this->withTenant();
});

test('testing environment works', function () {
    expect(true)->toBeTrue();
});

test('can create hall model', function () {
    $hall = Hall::factory()->create([
        'name' => 'Test Hall',
        'capacity' => 100,
    ]);
    
    expect($hall)
        ->toBeInstanceOf(Hall::class)
        ->and($hall->name)->toBe('Test Hall')
        ->and($hall->capacity)->toBe(100);
});

test('can create client model', function () {
    $client = Client::factory()->create([
        'name' => 'Test Client',
        'email' => 'test@example.com',
    ]);
    
    expect($client)
        ->toBeInstanceOf(Client::class)
        ->and($client->name)->toBe('Test Client')
        ->and($client->email)->toBe('test@example.com');
});

test('can create event with relationships', function () {
    $hall = Hall::factory()->create();
    $client = Client::factory()->create();
    
    $event = Event::factory()->create([
        'hall_id' => $hall->id,
        'client_id' => $client->id,
        'name' => 'Basic Event Test',
    ]);
    
    expect($event)
        ->toBeInstanceOf(Event::class)
        ->and($event->name)->toBe('Basic Event Test')
        ->and($event->hall_id)->toBe($hall->id)
        ->and($event->client_id)->toBe($client->id)
        ->and($event->tenant_id)->toBe(1);
});

test('models have correct table names', function () {
    expect((new Event())->getTable())->toBe('bhm_events')
        ->and((new Hall())->getTable())->toBe('bhm_halls')
        ->and((new Client())->getTable())->toBe('bhm_clients');
});

test('tenant scoping works', function () {
    // Create event with tenant 1
    $event1 = Event::factory()->create([
        'tenant_id' => 1,
        'name' => 'Tenant 1 Event'
    ]);
    
    // Set current tenant
    $this->withTenant(1);
    
    $events = Event::all();
    
    expect($events)->toHaveCount(1)
        ->and($events->first()->name)->toBe('Tenant 1 Event');
});