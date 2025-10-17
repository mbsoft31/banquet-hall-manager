<?php

namespace Mbsoft\BanquetHallManager\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Mbsoft\BanquetHallManager\Tests\Fixtures\User;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'tenant_id' => config('banquethallmanager.current_tenant_id', config('banquethallmanager.default_tenant_id', 1)),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'role' => 'admin',
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }
}
