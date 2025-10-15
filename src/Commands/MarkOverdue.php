<?php

namespace Mbsoft\BanquetHallManager\Commands;

use Illuminate\Console\Command;

class MarkOverdue extends Command
{
    protected $signature = 'bhm:mark-overdue';
    protected $description = 'Mark overdue invoices/bookings for Banquet Hall Manager';

    public function handle(): int
    {
        // Placeholder: wire actual logic to mark overdue items.
        $this->info('BHM: mark-overdue executed.');
        return self::SUCCESS;
    }
}

