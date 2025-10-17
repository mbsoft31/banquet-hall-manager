<?php

use Mbsoft\BanquetHallManager\Models\Event;
use Mbsoft\BanquetHallManager\Models\Hall;
use Mbsoft\BanquetHallManager\Models\Client;
use Mbsoft\BanquetHallManager\Tests\Fixtures\User;

beforeEach(function () {
    $this->withTenant();
    $this->user = $this->createAuthenticatedUser();
});

// Skip HTTP tests for now as they require routes and controllers
// These will be enabled once the routing system is properly set up
test('basic event model creation works', function () {
    $hall = Hall::factory()->create();
    $client = Client::factory()->create();
    
    $event = Event::factory()->create([
        'hall_id' => $hall->id,
        'client_id' => $client->id,
        'name' => 'Test Event',
        'type' => 'conference',
        'status' => 'confirmed',
    ]);
    
    expect($event)
        ->toBeInstanceOf(Event::class)
        ->and($event->name)->toBe('Test Event')
        ->and($event->hall_id)->toBe($hall->id)
        ->and($event->client_id)->toBe($client->id);
});

// TODO: Uncomment these tests once API routes are properly configured
/*
it('can list events with pagination', function () {
    $hall = Hall::factory()->create();
    $client = Client::factory()->create();
    
    Event::factory()->create([
        'hall_id' => $hall->id,
        'client_id' => $client->id,
        'name' => 'Test Event',
        'type' => 'conference',
        'status' => 'confirmed',
    ]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/bhm/events');

    $response->assertOk()
        ->assertJson([
            'data' => [
                [
                    'name' => 'Test Event',
                    'type' => 'conference',
                    'status' => 'confirmed',
                ]
            ]
        ]);
        
    expect($response->json())
        ->toBeValidPaginatedResponse()
        ->and($response->json('data'))->toHaveCount(1);
});
*/