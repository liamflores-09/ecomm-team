<?php

namespace App\Notifications;

use App\Models\CalendarTask;
use App\Models\User;
use Illuminate\Notifications\Notification;

class CalendarTaskNotification extends Notification
{
    public function __construct(
        private CalendarTask $task,
        private User $creator
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $due  = $this->task->due_date->format('M d');
        $role = ucfirst($this->task->assigned_role);

        return [
            'title'   => 'New Task Assigned',
            'message' => $this->creator->first_name . ' assigned "' . $this->task->title . '" to ' . $role . ' — due ' . $due,
            'icon'    => 'fa-list-check',
            'color'   => 'warning',
            'url'     => '/calendar',
        ];
    }
}
