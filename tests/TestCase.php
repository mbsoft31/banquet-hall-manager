<?php

namespace Mbsoft\BanquetHallManager\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Mbsoft\BanquetHallManager\BanquetHallManagerServiceProvider;
use Mbsoft\BanquetHallManager\Tests\Fixtures\User as TestUser;
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
        $app['config']->set('auth.providers.users.model', TestUser::class);
        $app['config']->set('banquethallmanager.default_tenant_id', 1);
        $app['config']->set('banquethallmanager.current_tenant_id', 1);
        $app['config']->set('banquethallmanager.multi_tenancy', true);
        $app['config']->set('banquethallmanager.enforce_tenant_header', false);
        $app['config']->set('banquethallmanager.tenant_model', TestUser::class);
        $app['config']->set('banquethallmanager.enable_tenant_scoping', true);
    }

    protected function setupTestData(): void
    {
        // Create a default tenant for testing
        $this->userModel()::factory()->create([
            'id' => 1,
            'name' => 'Test Tenant',
            'email' => 'tenant@test.com',
            'tenant_id' => 1,
        ]);
    }

    protected function createAuthenticatedUser(array $attributes = []): \Illuminate\Foundation\Auth\User
    {
        $model = $this->userModel();

        $attributes = array_merge([
            'tenant_id' => $attributes['tenant_id'] ?? ($attributes['id'] ?? config('banquethallmanager.current_tenant_id', config('banquethallmanager.default_tenant_id', 1))),
        ], $attributes);

        return $model::factory()->create($attributes);
    }

    protected function withTenant(int $tenantId = 1): static
    {
        config(['banquethallmanager.current_tenant_id' => $tenantId]);
        return $this;
    }

    protected function userModel(): string
    {
        return config('auth.providers.users.model');
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../src/Database/migrations');
    }

    protected function afterRefreshingDatabase()
    {
        $this->setupTestData();
    }
}
