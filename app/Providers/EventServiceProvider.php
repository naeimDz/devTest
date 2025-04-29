<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use App\Events\ServiceRequested;
use App\Events\RequestStatusUpdated;
use App\Events\UserRoleUpdated;
use App\Events\ServiceCreated;
use App\Listeners\NotifyAdminsAboutServiceCreated;
use App\Notifications\ServiceRequestedNotification;
use App\Notifications\RequestStatusUpdatedNotification;
use App\Notifications\UserRoleUpdatedNotification;
use App\Notifications\ServiceCreatedNotification;
use App\Listeners\SendNotificationListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ServiceRequested::class => [
            SendNotificationListener::class,'handleServiceRequested',
        ],
        RequestStatusUpdated::class => [
            SendNotificationListener::class,'handleRequestStatusUpdated',

        ],
        UserRoleUpdated::class => [
            SendNotificationListener::class,
        ],
        ServiceCreated::class => [
            NotifyAdminsAboutServiceCreated::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
