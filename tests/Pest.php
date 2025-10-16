<?php

use Mbsoft\BanquetHallManager\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses" function to bind a different classes or traits.
|
*/

uses(TestCase::class, RefreshDatabase::class)->in('Feature');
uses(TestCase::class, RefreshDatabase::class)->in('Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

expect()->extend('toHaveValidTimestamps', function () {
    return $this->toHaveKeys(['created_at', 'updated_at'])
        ->and($this->value['created_at'])->not->toBeNull()
        ->and($this->value['updated_at'])->not->toBeNull();
});

expect()->extend('toBeValidApiResource', function () {
    return $this->toHaveKeys(['data'])
        ->and($this->value['data'])->toBeArray();
});

expect()->extend('toBeValidPaginatedResponse', function () {
    return $this->toHaveKeys(['data', 'links', 'meta'])
        ->and($this->value['data'])->toBeArray()
        ->and($this->value['meta'])->toHaveKeys(['current_page', 'total', 'per_page']);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the amount of code duplication.
|
*/

function createTenant(array $attributes = []): \Illuminate\Database\Eloquent\Model
{
    // This assumes you have a Tenant model or User model acting as tenant
    // Adjust based on your actual tenant implementation
    return \Illuminate\Foundation\Auth\User::factory()->create(array_merge([
        'email' => 'tenant@example.com',
        'name' => 'Test Tenant',
    ], $attributes));
}

function actingAsTenant(\Illuminate\Database\Eloquent\Model $tenant = null): \Illuminate\Testing\TestResponse
{
    $tenant ??= createTenant();
    
    // Set the current tenant in your application
    // Adjust this based on how your tenant system works
    config(['banquethallmanager.current_tenant_id' => $tenant->id]);
    
    return test()->actingAs($tenant);
}

function withoutTenantScope(): void
{
    // Disable tenant scoping for testing
    config(['banquethallmanager.disable_tenant_scope' => true]);
}