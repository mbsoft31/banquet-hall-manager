<?php

use Mbsoft\BanquetHallManager\Models\Hall;
use Illuminate\Foundation\Auth\User;

beforeEach(function () {
    $this->withTenant();
    $this->user = $this->createAuthenticatedUser();
});

it('can list halls', function () {
    Hall::factory()->count(3)->create();

    $response = $this->actingAs($this->user)
        ->getJson('/api/bhm/halls');

    $response->assertOk();
    
    expect($response->json('data'))->toHaveCount(3);
});

it('can create a new hall', function () {
    $hallData = [
        'name' => 'Crystal Ballroom',
        'description' => 'Elegant ballroom with crystal chandeliers',
        'capacity' => 250,
        'hourly_rate' => 200.00,
    ];

    $response = $this->actingAs($this->user)
        ->postJson('/api/bhm/halls', $hallData);

    $response->assertCreated()
        ->assertJson([
            'data' => [
                'name' => 'Crystal Ballroom',
                'capacity' => 250,
                'hourly_rate' => 200.00,
            ]
        ]);

    $this->assertDatabaseHas('bhm_halls', [
        'name' => 'Crystal Ballroom',
        'capacity' => 250,
    ]);
});

it('validates required fields when creating hall', function () {
    $response = $this->actingAs($this->user)
        ->postJson('/api/bhm/halls', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'capacity', 'hourly_rate']);
});

it('can show a specific hall', function () {
    $hall = Hall::factory()->create([
        'name' => 'Garden Pavilion',
        'capacity' => 100,
    ]);

    $response = $this->actingAs($this->user)
        ->getJson("/api/bhm/halls/{$hall->id}");

    $response->assertOk()
        ->assertJson([
            'data' => [
                'id' => $hall->id,
                'name' => 'Garden Pavilion',
                'capacity' => 100,
            ]
        ]);
});

it('can update a hall', function () {
    $hall = Hall::factory()->create();
    
    $updateData = [
        'name' => 'Updated Hall Name',
        'capacity' => 300,
        'hourly_rate' => 175.00,
    ];

    $response = $this->actingAs($this->user)
        ->putJson("/api/bhm/halls/{$hall->id}", $updateData);

    $response->assertOk()
        ->assertJson([
            'data' => [
                'name' => 'Updated Hall Name',
                'capacity' => 300,
            ]
        ]);

    $this->assertDatabaseHas('bhm_halls', [
        'id' => $hall->id,
        'name' => 'Updated Hall Name',
        'capacity' => 300,
    ]);
});

it('can delete a hall', function () {
    $hall = Hall::factory()->create();

    $response = $this->actingAs($this->user)
        ->deleteJson("/api/bhm/halls/{$hall->id}");

    $response->assertNoContent();
    
    $this->assertDatabaseMissing('bhm_halls', ['id' => $hall->id]);
});