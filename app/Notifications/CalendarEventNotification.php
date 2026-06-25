<?php

namespace App\Notifications;

use App\Models\CalendarEvent;
use App\Models\User;
use Illuminate\Notifications\Notification;

class CalendarEventNotification extends Notification
{
    public function __construct(
        private CalendarEvent $event,
        private User $creator
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $start = $this->event->start_datetime->format('M d, g:i A');

        return [
            'title'   => 'New Event: ' . $this->event->title,
            'message' => $this->creator->first_name . ' added you to "' . $this->event->title . '" on ' . $start,
            'icon'    => 'fa-calendar-plus',
            'color'   => 'primary',
            'url'     => '/calendar',
        ];
    }
}
