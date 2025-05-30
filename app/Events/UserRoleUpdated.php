<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class UserRoleUpdated  implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $newRole;
    public int $userId;

    public function __construct( int $userId,int $newRole)
    {
        $this->newRole = $newRole;
        $this->userId = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.' . $this->userId),
        ];
    }
    
    public function broadcastWith()
    {
        return [
            'user_id' => $this->userId,
            'new_role' => $this->newRole,
            'notification' => 'Your role has been updated to ' . $this->newRole, 
        ];
    }
      public function broadcastAs()
    {
        return 'UserRoleUpdatedEvent';
    }
    
}
