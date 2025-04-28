<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ServiceRequestedNotification extends Notification
{
    use Queueable;

    public int $serviceId;

    public function __construct(int $serviceId)
    {
        $this->serviceId = $serviceId;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'You have a new service request!',
            'service_id' => $this->serviceId,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => 'You have a new service request!',
            'service_id' => $this->serviceId,
        ]);
    }
}
