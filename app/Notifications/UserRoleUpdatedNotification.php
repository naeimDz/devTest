<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class UserRoleUpdatedNotification extends Notification
{
    use Queueable;

    public int $userName;
    public int $newRole;

    public function __construct(int $userName, int $newRole)
    {
        $this->userName = $userName;
        $this->newRole = $newRole;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "تم تحديث دورك في المنصة.",
            'user_name' => $this->userName,
            'new_role' => $this->newRole,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => "تم تحديث دورك في المنصة.",
            'user_name' => $this->userName,
            'new_role' => $this->newRole,
        ]);
    }
}
