<?php

namespace Mbsoft\BanquetHallManager\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Mbsoft\BanquetHallManager\Models\Invoice;
use Mbsoft\BanquetHallManager\Notifications\OverdueInvoiceReminder;
use Carbon\Carbon;

class MarkOverdue extends Command
{
    protected $signature = 'bhm:mark-overdue';
    protected $description = 'Mark overdue invoices/bookings for Banquet Hall Manager';

    public function handle(): int
    {
        $now = Carbon::now()->toDateString();
        $candidates = Invoice::query()
            ->whereIn('status', ['pending', 'overdue'])
            ->whereDate('due_date', '<', $now)
            ->with('client')
            ->get();

        $marked = 0; $notified = 0;
        foreach ($candidates as $invoice) {
            if ($invoice->status !== 'overdue') {
                $invoice->status = 'overdue';
                $invoice->save();
                $marked++;
            }
            $client = $invoice->client;
            if ($client && !empty($client->email)) {
                Notification::route('mail', $client->email)
                    ->notify(new OverdueInvoiceReminder($invoice));
                $notified++;
            }
        }

        $this->info("Marked {$marked} invoices as overdue; sent {$notified} reminders.");
        return self::SUCCESS;
    }
}
