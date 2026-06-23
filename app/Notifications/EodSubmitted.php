<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Notification;

class EodSubmitted extends Notification
{
    public function __construct(private User $submitter, private string $date) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title'   => 'EOD Report Submitted',
            'message' => $this->submitter->first_name . ' ' . $this->submitter->last_name . ' submitted their end-of-day report for ' . \Carbon\Carbon::parse($this->date)->format('M d'),
            'icon'    => 'fa-calendar-check',
            'color'   => 'primary',
            'url'     => '/admin/daily-logs',
        ];
    }
}
