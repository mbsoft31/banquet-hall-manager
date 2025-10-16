<?php

use Mbsoft\BanquetHallManager\Http\Requests\Event\StoreEventRequest;
use Mbsoft\BanquetHallManager\Models\Hall;
use Mbsoft\BanquetHallManager\Models\Client;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Validator;

beforeEach(function () {
    $this->withTenant();
    $this->user = $this->createAuthenticatedUser();
    $this->hall = Hall::factory()->create();
    $this->client = Client::factory()->create();
});

it('passes validation with valid data', function () {
    $data = [
        'hall_id' => $this->hall->id,
        'client_id' => $this->client->id,
        'name' => 'Wedding Reception',
        'type' => 'wedding',
        'start_at' => now()->addDays(30)->format('Y-m-d H:i:s'),
        'end_at' => now()->addDays(30)->addHours(6)->format('Y-m-d H:i:s'),
        'guest_count' => 150,
        'status' => 'pending',
        'total_amount' => 5000.00,
    ];
    
    $request = new StoreEventRequest();
    $validator = Validator::make($data, $request->rules());
    
    expect($validator->passes())->toBeTrue();
});

it('fails validation with missing required fields', function () {
    $data = [
        'name' => 'Incomplete Event',
        // Missing required fields
    ];
    
    $request = new StoreEventRequest();
    $validator = Validator::make($data, $request->rules());
    
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('hall_id'))->toBeTrue()
        ->and($validator->errors()->has('client_id'))->toBeTrue()
        ->and($validator->errors()->has('start_at'))->toBeTrue()
        ->and($validator->errors()->has('end_at'))->toBeTrue();
});

it('fails validation when end_at is before start_at', function () {
    $data = [
        'hall_id' => $this->hall->id,
        'client_id' => $this->client->id,
        'name' => 'Invalid Event',
        'type' => 'conference',
        'start_at' => now()->addDays(30)->format('Y-m-d H:i:s'),
        'end_at' => now()->addDays(29)->format('Y-m-d H:i:s'), // Before start_at
        'guest_count' => 100,
    ];
    
    $request = new StoreEventRequest();
    $validator = Validator::make($data, $request->rules());
    
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('end_at'))->toBeTrue();
});

it('validates guest_count is positive integer', function () {
    $data = [
        'hall_id' => $this->hall->id,
        'client_id' => $this->client->id,
        'name' => 'Test Event',
        'start_at' => now()->addDays(30)->format('Y-m-d H:i:s'),
        'end_at' => now()->addDays(30)->addHours(4)->format('Y-m-d H:i:s'),
        'guest_count' => -5, // Invalid negative number
    ];
    
    $request = new StoreEventRequest();
    $validator = Validator::make($data, $request->rules());
    
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('guest_count'))->toBeTrue();
});

it('validates total_amount is numeric and positive', function () {
    $invalidAmounts = [-100, 'invalid', null];
    
    foreach ($invalidAmounts as $amount) {
        $data = [
            'hall_id' => $this->hall->id,
            'client_id' => $this->client->id,
            'name' => 'Test Event',
            'start_at' => now()->addDays(30)->format('Y-m-d H:i:s'),
            'end_at' => now()->addDays(30)->addHours(4)->format('Y-m-d H:i:s'),
            'total_amount' => $amount,
        ];
        
        $request = new StoreEventRequest();
        $validator = Validator::make($data, $request->rules());
        
        expect($validator->fails())->toBeTrue("Failed for amount: {$amount}");
    }
});