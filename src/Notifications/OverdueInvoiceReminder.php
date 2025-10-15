<?php

namespace Mbsoft\BanquetHallManager\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Mbsoft\BanquetHallManager\Models\Invoice;

class OverdueInvoiceReminder extends Notification
{
    use Queueable;

    public function __construct(private readonly Invoice $invoice) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Invoice Overdue Notice')
            ->greeting('Hello '.$notifiable->name)
            ->line('Invoice '.$this->invoice->invoice_number.' is overdue.')
            ->line('Total: '.$this->invoice->total_amount.' Due: '.$this->invoice->due_date)
            ->line('Please make a payment at your earliest convenience.');
    }
}

