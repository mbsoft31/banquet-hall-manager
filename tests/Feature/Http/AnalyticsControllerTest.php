<?php

use Mbsoft\BanquetHallManager\Models\Event;
use Mbsoft\BanquetHallManager\Models\Hall;
use Mbsoft\BanquetHallManager\Models\Client;
use Mbsoft\BanquetHallManager\Models\Invoice;
use Mbsoft\BanquetHallManager\Models\Payment;
use Illuminate\Foundation\Auth\User;

beforeEach(function () {
    $this->withTenant();
    $this->user = $this->createAuthenticatedUser();
});

it('provides revenue analytics', function () {
    // Create test data for analytics
    $hall = Hall::factory()->create();
    $client = Client::factory()->create();
    
    // Create events in different months
    Event::factory()->create([
        'hall_id' => $hall->id,
        'client_id' => $client->id,
        'total_amount' => 5000.00,
        'status' => 'completed',
        'start_at' => now()->subMonth(),
    ]);
    
    Event::factory()->create([
        'hall_id' => $hall->id,
        'client_id' => $client->id,
        'total_amount' => 3000.00,
        'status' => 'completed',
        'start_at' => now()->subDays(15),
    ]);

    // Create paid invoices
    Invoice::factory()->count(2)->create([
        'status' => 'paid',
        'total_amount' => 2500.00,
    ]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/bhm/analytics/revenue');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'total_revenue',
                'monthly_revenue',
                'completed_events',
                'average_event_value'
            ]
        ]);

    $data = $response->json('data');
    expect($data['completed_events'])->toBe(2)
        ->and($data['total_revenue'])->toBeGreaterThan(0);
});

it('provides booking analytics by hall', function () {
    $hall1 = Hall::factory()->create(['name' => 'Grand Ballroom']);
    $hall2 = Hall::factory()->create(['name' => 'Garden Pavilion']);
    $client = Client::factory()->create();
    
    // Create more events for hall1
    Event::factory()->count(3)->create([
        'hall_id' => $hall1->id,
        'client_id' => $client->id,
        'status' => 'completed',
    ]);
    
    Event::factory()->count(1)->create([
        'hall_id' => $hall2->id,
        'client_id' => $client->id,
        'status' => 'completed',
    ]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/bhm/analytics/bookings-by-hall');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'hall_name',
                    'total_events',
                    'total_revenue'
                ]
            ]
        ]);

    $halls = collect($response->json('data'));
    $grandBallroom = $halls->firstWhere('hall_name', 'Grand Ballroom');
    
    expect($grandBallroom['total_events'])->toBe(3);
});

it('provides monthly trends', function () {
    $hall = Hall::factory()->create();
    $client = Client::factory()->create();
    
    // Create events across different months
    for ($i = 0; $i < 6; $i++) {
        Event::factory()->create([
            'hall_id' => $hall->id,
            'client_id' => $client->id,
            'start_at' => now()->subMonths($i),
            'status' => 'completed',
            'total_amount' => 2000.00,
        ]);
    }

    $response = $this->actingAs($this->user)
        ->getJson('/api/bhm/analytics/monthly-trends');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'month',
                    'events_count',
                    'revenue'
                ]
            ]
        ]);

    expect($response->json('data'))->toHaveCount(6);
});