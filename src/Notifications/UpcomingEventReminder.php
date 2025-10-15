<?php

namespace Mbsoft\BanquetHallManager\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Mbsoft\BanquetHallManager\Models\Event;

class UpcomingEventReminder extends Notification
{
    use Queueable;

    public function __construct(private readonly Event $event) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Upcoming Event Reminder')
            ->greeting('Hello '.$notifiable->name)
            ->line('This is a reminder for your upcoming event: '.$this->event->name)
            ->line('Date: '.$this->event->start_at?->toDayDateTimeString().' - '.$this->event->end_at?->toDayDateTimeString())
            ->line('Hall: '.$this->event->hall?->name)
            ->line('Thank you for choosing us.');
    }
}

