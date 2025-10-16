<?php

use Mbsoft\BanquetHallManager\Models\Client;
use Mbsoft\BanquetHallManager\Models\Event;

beforeEach(function () {
    $this->withTenant();
});

it('can create a client', function () {
    $client = Client::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '+1234567890',
    ]);

    expect($client)
        ->toBeInstanceOf(Client::class)
        ->and($client->name)->toBe('John Doe')
        ->and($client->email)->toBe('john@example.com')
        ->and($client->phone)->toBe('+1234567890');
});

it('has correct fillable attributes', function () {
    $client = new Client();
    
    expect($client->getFillable())->toContain(
        'tenant_id',
        'name',
        'email',
        'phone',
        'address'
    );
});

it('has many events', function () {
    $client = Client::factory()->create();
    $event1 = Event::factory()->create(['client_id' => $client->id]);
    $event2 = Event::factory()->create(['client_id' => $client->id]);

    expect($client->events)
        ->toHaveCount(2)
        ->and($client->events->pluck('id')->toArray())->toContain($event1->id, $event2->id);
});

it('uses correct table name', function () {
    $client = new Client();
    expect($client->getTable())->toBe('bhm_clients');
});