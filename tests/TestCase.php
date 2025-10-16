<?php

namespace Mbsoft\BanquetHallManager\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Mbsoft\BanquetHallManager\BanquetHallManagerServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Register factories with correct namespace
        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Mbsoft\\BanquetHallManager\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

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
        $app['config']->set('banquethallmanager.enable_tenant_scoping', true);
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