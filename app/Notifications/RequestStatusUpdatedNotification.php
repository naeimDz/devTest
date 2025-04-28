<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class RequestStatusUpdatedNotification extends Notification
{
    use Queueable;

    public string $newStatus;

    public function __construct(string $newStatus)
    {
        $this->newStatus = $newStatus;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Your service request status has been updated.',
            'new_status' => $this->newStatus,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => 'Your service request status has been updated.',
            'new_status' => $this->newStatus,
        ]);
    }
}
