<?php

use Mbsoft\BanquetHallManager\Models\Event;
use Mbsoft\BanquetHallManager\Models\Hall;
use Mbsoft\BanquetHallManager\Models\Client;
use Illuminate\Foundation\Auth\User;

beforeEach(function () {
    $this->withTenant();
    $this->user = $this->createAuthenticatedUser();
});

it('can list events with pagination', function () {
    actingAsTenant()
        ->postJson('/api/bhm/events', [
            'hall_id' => Hall::factory()->create()->id,
            'client_id' => Client::factory()->create()->id,
            'name' => 'Test Event',
            'type' => 'conference',
            'start_at' => now()->addDays(7)->format('Y-m-d H:i:s'),
            'end_at' => now()->addDays(7)->addHours(4)->format('Y-m-d H:i:s'),
            'guest_count' => 100,
            'status' => 'confirmed',
            'total_amount' => 2500.00,
        ])->dump();

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

it('can filter events by status', function () {
    $hall = Hall::factory()->create();
    $client = Client::factory()->create();
    
    Event::factory()->create([
        'hall_id' => $hall->id,
        'client_id' => $client->id,
        'status' => 'confirmed',
        'name' => 'Confirmed Event'
    ]);
    
    Event::factory()->create([
        'hall_id' => $hall->id,
        'client_id' => $client->id,
        'status' => 'pending',
        'name' => 'Pending Event'
    ]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/bhm/events?status=confirmed');

    $response->assertOk();
    
    expect($response->json('data'))
        ->toHaveCount(1)
        ->and($response->json('data.0.name'))->toBe('Confirmed Event');
});

it('can create a new event', function () {
    $hall = Hall::factory()->create();
    $client = Client::factory()->create();
    
    $eventData = [
        'hall_id' => $hall->id,
        'client_id' => $client->id,
        'name' => 'New Wedding Event',
        'type' => 'wedding',
        'start_at' => now()->addDays(30)->format('Y-m-d H:i:s'),
        'end_at' => now()->addDays(30)->addHours(6)->format('Y-m-d H:i:s'),
        'guest_count' => 200,
        'status' => 'pending',
        'total_amount' => 8000.00,
        'special_requests' => ['live_band', 'vegan_menu']
    ];

    $response = $this->actingAs($this->user)
        ->postJson('/api/bhm/events', $eventData);

    $response->assertCreated()
        ->assertJson([
            'data' => [
                'name' => 'New Wedding Event',
                'type' => 'wedding',
                'status' => 'pending',
                'guest_count' => 200,
            ]
        ]);

    $this->assertDatabaseHas('bhm_events', [
        'name' => 'New Wedding Event',
        'hall_id' => $hall->id,
        'client_id' => $client->id,
    ]);
});

it('validates required fields when creating event', function () {
    $response = $this->actingAs($this->user)
        ->postJson('/api/bhm/events', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['hall_id', 'client_id', 'name', 'start_at', 'end_at']);
});

it('can show a specific event', function () {
    $event = Event::factory()->create([
        'name' => 'Birthday Party',
        'type' => 'birthday'
    ]);

    $response = $this->actingAs($this->user)
        ->getJson("/api/bhm/events/{$event->id}");

    $response->assertOk()
        ->assertJson([
            'data' => [
                'id' => $event->id,
                'name' => 'Birthday Party',
                'type' => 'birthday',
            ]
        ]);
});

it('can update an event', function () {
    $event = Event::factory()->create();
    
    $updateData = [
        'name' => 'Updated Event Name',
        'guest_count' => 150,
        'status' => 'confirmed'
    ];

    $response = $this->actingAs($this->user)
        ->putJson("/api/bhm/events/{$event->id}", $updateData);

    $response->assertOk()
        ->assertJson([
            'data' => [
                'name' => 'Updated Event Name',
                'guest_count' => 150,
                'status' => 'confirmed',
            ]
        ]);

    $this->assertDatabaseHas('bhm_events', [
        'id' => $event->id,
        'name' => 'Updated Event Name',
        'guest_count' => 150,
    ]);
});

it('can delete an event', function () {
    $event = Event::factory()->create();

    $response = $this->actingAs($this->user)
        ->deleteJson("/api/bhm/events/{$event->id}");

    $response->assertNoContent();
    
    $this->assertDatabaseMissing('bhm_events', ['id' => $event->id]);
});

it('can reschedule an event', function () {
    $event = Event::factory()->create();
    
    $newSchedule = [
        'start_at' => now()->addDays(45)->format('Y-m-d H:i:s'),
        'end_at' => now()->addDays(45)->addHours(5)->format('Y-m-d H:i:s'),
    ];

    $response = $this->actingAs($this->user)
        ->patchJson("/api/bhm/events/{$event->id}/reschedule", $newSchedule);

    $response->assertOk();
    
    $event->refresh();
    expect($event->start_at->format('Y-m-d H:i:s'))->toBe($newSchedule['start_at'])
        ->and($event->end_at->format('Y-m-d H:i:s'))->toBe($newSchedule['end_at']);
});

it('prevents scheduling conflicts when rescheduling', function () {
    $hall = Hall::factory()->create();
    
    // Create an existing event
    Event::factory()->create([
        'hall_id' => $hall->id,
        'start_at' => now()->addDays(10)->setHour(18),
        'end_at' => now()->addDays(10)->setHour(22),
    ]);
    
    // Create another event to reschedule
    $eventToReschedule = Event::factory()->create([
        'hall_id' => $hall->id,
        'start_at' => now()->addDays(15),
        'end_at' => now()->addDays(15)->addHours(4),
    ]);

    // Try to reschedule to conflicting time
    $response = $this->actingAs($this->user)
        ->patchJson("/api/bhm/events/{$eventToReschedule->id}/reschedule", [
            'start_at' => now()->addDays(10)->setHour(19)->format('Y-m-d H:i:s'),
            'end_at' => now()->addDays(10)->setHour(21)->format('Y-m-d H:i:s'),
        ]);

    $response->assertUnprocessable()
        ->assertJson(['message' => 'Scheduling conflict for hall.']);
});

it('can cancel an event', function () {
    $event = Event::factory()->create(['status' => 'confirmed']);

    $response = $this->actingAs($this->user)
        ->patchJson("/api/bhm/events/{$event->id}/cancel");

    $response->assertOk();
    
    $event->refresh();
    expect($event->status)->toBe('cancelled');
});

it('can sort events by different fields', function () {
    $hall = Hall::factory()->create();
    $client = Client::factory()->create();
    
    Event::factory()->create([
        'hall_id' => $hall->id,
        'client_id' => $client->id,
        'name' => 'B Event',
        'start_at' => now()->addDays(2),
    ]);
    
    Event::factory()->create([
        'hall_id' => $hall->id,
        'client_id' => $client->id,
        'name' => 'A Event',
        'start_at' => now()->addDays(1),
    ]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/bhm/events?sort=name&direction=asc');

    $events = $response->json('data');
    expect($events[0]['name'])->toBe('A Event')
        ->and($events[1]['name'])->toBe('B Event');
});
