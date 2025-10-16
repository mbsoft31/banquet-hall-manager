<?php

use Mbsoft\BanquetHallManager\Models\Event;
use Mbsoft\BanquetHallManager\Policies\EventPolicy;
use Illuminate\Foundation\Auth\User;

beforeEach(function () {
    $this->withTenant();
    $this->policy = new EventPolicy();
    $this->user = $this->createAuthenticatedUser();
});

it('allows viewing any events for authorized users', function () {
    expect($this->policy->viewAny($this->user))->toBeTrue();
});

it('allows viewing specific events for authorized users', function () {
    $event = Event::factory()->create();
    
    expect($this->policy->view($this->user, $event))->toBeTrue();
});

it('allows creating events for authorized users', function () {
    expect($this->policy->create($this->user))->toBeTrue();
});

it('allows updating events for authorized users', function () {
    $event = Event::factory()->create();
    
    expect($this->policy->update($this->user, $event))->toBeTrue();
});

it('allows deleting events for authorized users', function () {
    $event = Event::factory()->create();
    
    expect($this->policy->delete($this->user, $event))->toBeTrue();
});

it('denies access to unauthorized users', function () {
    $unauthorizedUser = User::factory()->create(['role' => 'guest']);
    $event = Event::factory()->create();
    
    expect($this->policy->view($unauthorizedUser, $event))->toBeFalse()
        ->and($this->policy->create($unauthorizedUser))->toBeFalse()
        ->and($this->policy->update($unauthorizedUser, $event))->toBeFalse()
        ->and($this->policy->delete($unauthorizedUser, $event))->toBeFalse();
});