<?php

use Mbsoft\BanquetHallManager\Models\Hall;
use Mbsoft\BanquetHallManager\Models\Event;

beforeEach(function () {
    $this->withTenant();
});

it('can create a hall', function () {
    $hall = Hall::factory()->create([
        'name' => 'Grand Ballroom',
        'capacity' => 300,
        'hourly_rate' => 150.00,
    ]);

    expect($hall)
        ->toBeInstanceOf(Hall::class)
        ->and($hall->name)->toBe('Grand Ballroom')
        ->and($hall->capacity)->toBe(300)
        ->and($hall->hourly_rate)->toBe(150.00);
});

it('has correct fillable attributes', function () {
    $hall = new Hall();
    
    expect($hall->getFillable())->toContain(
        'tenant_id',
        'name',
        'description',
        'capacity',
        'hourly_rate'
    );
});

it('has many events', function () {
    $hall = Hall::factory()->create();
    $event1 = Event::factory()->create(['hall_id' => $hall->id]);
    $event2 = Event::factory()->create(['hall_id' => $hall->id]);

    expect($hall->events)
        ->toHaveCount(2)
        ->and($hall->events->pluck('id')->toArray())->toContain($event1->id, $event2->id);
});

it('uses correct table name', function () {
    $hall = new Hall();
    expect($hall->getTable())->toBe('bhm_halls');
});

it('casts hourly_rate to decimal', function () {
    $hall = Hall::factory()->create(['hourly_rate' => 250.75]);
    
    expect($hall->hourly_rate)
        ->toBeFloat()
        ->and($hall->hourly_rate)->toBe(250.75);
});