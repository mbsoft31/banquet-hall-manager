<?php

use Mbsoft\BanquetHallManager\Models\Client;
use Mbsoft\BanquetHallManager\Models\Event;

beforeEach(function () {
    $this->withTenant();
});

test('can create a client', function () {
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

test('has correct fillable attributes', function () {
    $client = new Client();
    
    expect($client->getFillable())->toContain(
        'tenant_id',
        'name',
        'email',
        'phone',
        'address'
    );
});

test('has many events', function () {
    $client = Client::factory()->create();
    Event::factory()->count(2)->create(['client_id' => $client->id]);

    $client = $client->fresh(['events']);
    
    expect($client->events)->toHaveCount(2);
});

test('uses correct table name', function () {
    $client = new Client();
    expect($client->getTable())->toBe('bhm_clients');
});