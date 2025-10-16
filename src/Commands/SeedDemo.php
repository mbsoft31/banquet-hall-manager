<?php

namespace Mbsoft\BanquetHallManager\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Mbsoft\BanquetHallManager\Database\seeders\BhmDemoSeeder;

class SeedDemo extends Command
{
    protected $signature = 'bhm:seed-demo {--tenant=1 : Tenant ID to seed}';

    protected $description = 'Seed demo data for Banquet Hall Manager (halls, clients, services, event, invoice, payment).';

    public function handle(): int
    {
        $tenant = (int) $this->option('tenant');
        Config::set('banquethallmanager.demo_tenant_id', $tenant);

        $this->info("Seeding demo data for tenant {$tenant}...");

        $this->call('db:seed', [
            '--class' => BhmDemoSeeder::class,
            '--force' => true,
        ]);

        $this->info('Demo data seeded. You can now query /api/bhm/* endpoints.');
        return self::SUCCESS;
    }
}

