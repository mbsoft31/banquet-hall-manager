<?php

namespace Mbsoft\BanquetHallManager\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SeedRoles extends Command
{
    protected $signature = 'bhm:seed-roles';
    protected $description = 'Seed BHM roles and permissions (requires Spatie permissions)';

    public function handle(): int
    {
        $class = 'Mbsoft\\BanquetHallManager\\Database\\seeders\\BhmRolesAndPermissionsSeeder';
        try {
            Artisan::call('db:seed', ['--class' => $class, '--force' => true]);
            $this->line(Artisan::output());
            $this->info('BHM roles & permissions seeding completed.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Failed to seed roles/permissions: '.$e->getMessage());
            return self::FAILURE;
        }
    }
}

