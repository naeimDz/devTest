<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\Service;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class ServiceCreatedNotification extends Notification
{
    use Queueable;

    public $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }


    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'service_id' => $this->service->id,
            'service_name' => $this->service->name,
            'message' => "تم إضافة خدمة جديدة: " . $this->service->name,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'service_id' => $this->service->id,
            'service_name' => $this->service->name,
            'message' => "تم إضافة خدمة جديدة: " . $this->service->name,
        ]);
    }
}
