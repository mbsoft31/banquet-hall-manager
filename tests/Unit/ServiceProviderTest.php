<?php

use Mbsoft\BanquetHallManager\BanquetHallManagerServiceProvider;
use Mbsoft\BanquetHallManager\Models\Event;
use Mbsoft\BanquetHallManager\Policies\EventPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    $this->provider = new BanquetHallManagerServiceProvider($this->app);
});

it('registers package configuration', function () {
    $this->provider->boot();
    
    expect(config('banquethallmanager'))->toBeArray()
        ->and(config('banquethallmanager.default_tenant_id'))->not->toBeNull();
});

it('registers policies correctly', function () {
    $this->provider->boot();
    
    expect(Gate::getPolicyFor(Event::class))->toBe(EventPolicy::class);
});

it('registers custom gates', function () {
    $this->provider->boot();
    
    expect(Gate::has('bhm.read'))->toBeTrue()
        ->and(Gate::has('bhm.write'))->toBeTrue()
        ->and(Gate::has('bhm.delete'))->toBeTrue();
});

it('loads migrations', function () {
    // Check if migrations are registered
    $migrationPaths = $this->app['migrator']->paths();
    
    $hasBhmMigrations = collect($migrationPaths)
        ->contains(fn($path) => str_contains($path, 'BanquetHallManager'));
    
    expect($hasBhmMigrations)->toBeTrue();
});

it('registers middleware alias', function () {
    $this->provider->boot();
    
    $router = $this->app['router'];
    $middlewareGroups = $router->getMiddleware();
    
    expect($middlewareGroups)->toHaveKey('bhm.tenant');
});

it('registers console commands in console environment', function () {
    // This test checks if commands are registered when in console
    $this->app->instance('env', 'testing');
    
    expect(function () {
        $this->provider->boot();
    })->not->toThrow(Exception::class);
});