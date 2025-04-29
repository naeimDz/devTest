<?php

namespace App\Listeners;

use App\Events\ServiceCreated;
use App\Models\User;
use App\Notifications\ServiceCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyAdminsAboutServiceCreated
{
    /**
     * Create the event listener.
     */


    /**
     * Handle the event.
     */
    public function handle(ServiceCreated $event): void
    {
        $service = $event->service;
        $admins = User::where('role_id', '1')->get();
        foreach ($admins as $admin) {
            $admin->notify(new ServiceCreatedNotification($service));
        }
        
    }
}
