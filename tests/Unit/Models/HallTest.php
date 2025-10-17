<?php

use Mbsoft\BanquetHallManager\Models\Hall;
use Mbsoft\BanquetHallManager\Models\Event;

beforeEach(function () {
    $this->withTenant();
});

test('can create a hall', function () {
    $hall = Hall::factory()->create([
        'name' => 'Grand Ballroom',
        'capacity' => 300,
        'hourly_rate' => 150.00,
    ]);

    expect($hall)
        ->toBeInstanceOf(Hall::class)
        ->and($hall->name)->toBe('Grand Ballroom')
        ->and($hall->capacity)->toBe(300)
        ->and($hall->hourly_rate)->toEqual('150.00'); // Decimal returns string
});

test('has correct fillable attributes', function () {
    $hall = new Hall();
    
    expect($hall->getFillable())->toContain(
        'tenant_id',
        'name',
        'description',
        'capacity',
        'hourly_rate'
    );
});

test('has many events', function () {
    $hall = Hall::factory()->create();
    Event::factory()->count(2)->create(['hall_id' => $hall->id]);

    $hall = $hall->fresh(['events']);

    expect($hall->events)->toHaveCount(2);
});

test('uses correct table name', function () {
    $hall = new Hall();
    expect($hall->getTable())->toBe('bhm_halls');
});

test('casts hourly_rate to decimal', function () {
    $hall = Hall::factory()->create(['hourly_rate' => 250.75]);
    
    expect($hall->hourly_rate)->toEqual('250.75'); // Decimal cast returns string
});