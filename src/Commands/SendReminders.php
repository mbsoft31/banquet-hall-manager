<?php

namespace Mbsoft\BanquetHallManager\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Mbsoft\BanquetHallManager\Models\Event;
use Mbsoft\BanquetHallManager\Models\Client;
use Mbsoft\BanquetHallManager\Notifications\UpcomingEventReminder;
use Carbon\Carbon;

class SendReminders extends Command
{
    protected $signature = 'bhm:send-reminders';
    protected $description = 'Send reminder notifications for upcoming events';

    public function handle(): int
    {
        $hours = (int) config('banquethallmanager.reminder_hours', 24);
        $from = Carbon::now();
        $to = Carbon::now()->addHours($hours);
        $events = Event::query()
            ->where('status', 'confirmed')
            ->whereBetween('start_at', [$from, $to])
            ->with(['client', 'hall'])
            ->get();

        $count = 0;
        foreach ($events as $event) {
            $client = $event->client;
            if (!$client || empty($client->email)) {
                continue;
            }
            Notification::route('mail', $client->email)
                ->notify(new UpcomingEventReminder($event));
            $count++;
        }

        $this->info("Reminders sent for {$count} upcoming events (next {$hours}h).");
        return self::SUCCESS;
    }
}
