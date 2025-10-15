<?php

namespace Mbsoft\BanquetHallManager\Commands;

use Illuminate\Console\Command;

class SendReminders extends Command
{
    protected $signature = 'bhm:send-reminders';
    protected $description = 'Send reminder notifications for upcoming events';

    public function handle(): int
    {
        // Placeholder: wire actual reminder notifications.
        $this->info('BHM: send-reminders executed.');
        return self::SUCCESS;
    }
}

