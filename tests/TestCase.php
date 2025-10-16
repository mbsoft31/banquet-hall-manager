<?php

namespace Mbsoft\BanquetHallManager\Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mbsoft\BanquetHallManager\BanquetHallManagerServiceProvider;
use Orchestra\Testbench\Concerns\WithWorkbench;

abstract class TestCase extends BaseTestCase
{
    use WithWorkbench;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__ . '/../src/Database/migrations');
        
        // Seed basic data needed for tests
        $this->setupTestData();
    }

    protected function getPackageProviders($app): array
    {
        return [
            BanquetHallManagerServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        // Setup the application environment for testing
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Set package configuration
        $app['config']->set('banquethallmanager.default_tenant_id', 1);
        $app['config']->set('banquethallmanager.tenant_model', \Illuminate\Foundation\Auth\User::class);
    }

    protected function setupTestData(): void
    {
        // Create a default tenant for testing
        \Illuminate\Foundation\Auth\User::factory()->create([
            'id' => 1,
            'name' => 'Test Tenant',
            'email' => 'tenant@test.com',
        ]);
    }

    protected function createAuthenticatedUser(array $attributes = []): \Illuminate\Foundation\Auth\User
    {
        return \Illuminate\Foundation\Auth\User::factory()->create($attributes);
    }

    protected function withTenant(int $tenantId = 1): static
    {
        config(['banquethallmanager.current_tenant_id' => $tenantId]);
        return $this;
    }
}