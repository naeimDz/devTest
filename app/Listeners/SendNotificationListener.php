<?php

namespace App\Listeners;

use App\Events\ServiceRequested;
use App\Events\RequestStatusUpdated;
use App\Events\UserRoleUpdated;
use App\Events\ServiceCreated;
use App\Notifications\ServiceRequestedNotification;
use App\Notifications\RequestStatusUpdatedNotification;
use App\Notifications\UserRoleUpdatedNotification;
use App\Notifications\ServiceCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNotificationListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ServiceRequested $event)
    {
        $user = User::find($event->userId);
        $user->notify(new ServiceRequestedNotification($event->serviceId));
    }

    public function handleRequestStatusUpdated(RequestStatusUpdated $event)
    {
        $user = User::find($event->requestId);
        $user->notify(new RequestStatusUpdatedNotification($event->newStatus));
    }

    public function handleUserRoleUpdated(UserRoleUpdated $event)
    {
        $user = User::find($event->userId);
        $user->notify(new UserRoleUpdatedNotification($event->newRole));
    }

    public function handleServiceCreated(ServiceCreated $event)
    {
        $admins = User->Role::where('role_id', '1')->get();
        foreach ($admins as $admin) {
            $admin->notify(new ServiceCreatedNotification($event->serviceId));
        }
    }
}
