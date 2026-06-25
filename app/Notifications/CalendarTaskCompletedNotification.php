<?php

namespace App\Notifications;

use App\Models\CalendarTask;
use App\Models\User;
use Illuminate\Notifications\Notification;

class CalendarTaskCompletedNotification extends Notification
{
    public function __construct(
        private CalendarTask $task,
        private User $completedBy
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title'   => 'Task Completed',
            'message' => $this->completedBy->first_name . ' completed "' . $this->task->title . '"',
            'icon'    => 'fa-circle-check',
            'color'   => 'success',
            'url'     => '/calendar',
        ];
    }
}
