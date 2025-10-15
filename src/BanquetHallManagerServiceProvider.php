<?php

namespace Mbsoft\BanquetHallManager;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Gate;

use Mbsoft\BanquetHallManager\Models\Client;
use Mbsoft\BanquetHallManager\Models\Event;
use Mbsoft\BanquetHallManager\Models\Hall;
use Mbsoft\BanquetHallManager\Policies\ClientPolicy;
use Mbsoft\BanquetHallManager\Policies\EventPolicy;
use Mbsoft\BanquetHallManager\Policies\HallPolicy;

class BanquetHallManagerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register package bindings here as implementation grows.
        // Example: $this->app->bind(Contracts\EventRepositoryInterface::class, Repositories\EventRepository::class);
    }

    public function boot(): void
    {
        // Merge default config so package works without publishing
        $this->mergeConfigFrom(__DIR__.'/Config/banquethallmanager.php', 'banquethallmanager');

        $this->publishes([
            __DIR__.'/Config/banquethallmanager.php' => config_path('banquethallmanager.php'),
        ], 'config');

        $this->loadMigrationsFrom(__DIR__.'/Database/migrations');
        $this->loadRoutesFrom(__DIR__.'/Routes/api.php');

        // Register policies
        Gate::policy(Client::class, ClientPolicy::class);
        Gate::policy(Event::class, EventPolicy::class);
        Gate::policy(Hall::class, HallPolicy::class);

        $this->app->afterResolving(Schedule::class, function (Schedule $schedule): void {
            $schedule->command('bhm:mark-overdue')->hourly();
            $schedule->command('bhm:send-reminders')->dailyAt('09:00');
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\MarkOverdue::class,
                Commands\SendReminders::class,
            ]);
        }
    }
}
