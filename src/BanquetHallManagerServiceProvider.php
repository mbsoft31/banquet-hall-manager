<?php

namespace Mbsoft\BanquetHallManager;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class BanquetHallManagerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register package bindings here as implementation grows.
        // Example: $this->app->bind(Contracts\EventRepositoryInterface::class, Repositories\EventRepository::class);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/Config/banquethallmanager.php' => config_path('banquethallmanager.php'),
        ], 'config');

        $this->loadMigrationsFrom(__DIR__.'/Database/migrations');
        $this->loadRoutesFrom(__DIR__.'/Routes/api.php');

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

