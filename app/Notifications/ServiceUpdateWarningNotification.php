<?php

namespace App\Notifications;

use App\Models\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class ServiceUpdateWarningNotification extends Notification
{
    use Queueable;

    public Service $service;

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
            'name' => $this->service->name,
            'message' => 'تم تعديل الخدمة الخاصة بك من طرف الإدارة.',
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'service_id' => $this->service->id,
            'name' => $this->service->name,
            'message' => 'تم تعديل الخدمة الخاصة بك من طرف الإدارة.',
        ]);
    }
}
