<?php

namespace App\Providers;

use App\Listeners\Screens\ScreenStatusOffline;
use App\Listeners\Screens\NotifyAdminScreenOffline;
use App\Events\Screens\OnlineEvent;
use App\Events\UpdateScheduleEvent;
use App\Events\UpdateScreenPlaylistEvent;
use App\Events\EmergencyEvent;
use App\Events\Screens\FirstPingEvent;
use App\Events\Screens\OfflineEvent;
use App\Events\UpdateAnnouncementEvent;
use App\Listeners\EmergencyNotificationsListener;
use App\Listeners\IncreaseVersionListener;
use App\Listeners\LogEmergencyListener;
use App\Listeners\Screens\NotifyAdminScreenAvailable;
use App\Listeners\Screens\NotifyAdminScreenOnline;
use App\Listeners\Screens\RebootScreenListener;
use App\Listeners\Screens\ScreenStatusOnline;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

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
        OfflineEvent::class => [
            ScreenStatusOffline::class,
            NotifyAdminScreenOffline::class,
            RebootScreenListener::class,
        ],
        OnlineEvent::class => [
            ScreenStatusOnline::class,
            NotifyAdminScreenOnline::class,
        ],
        FirstPingEvent::class => [
            NotifyAdminScreenAvailable::class,
            ScreenStatusOnline::class
        ],
        EmergencyEvent::class => [
            EmergencyNotificationsListener::class,
            LogEmergencyListener::class,
        ],
        UpdateAnnouncementEvent::class => [
            IncreaseVersionListener::class,
        ],
        UpdateScheduleEvent::class => [
            IncreaseVersionListener::class,
        ],
        UpdateScreenPlaylistEvent::class => [
            IncreaseVersionListener::class,
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
