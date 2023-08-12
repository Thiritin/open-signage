<?php

namespace App\Providers;

use App\Events\Screens\FirstPingEvent;
use App\Events\Screens\OfflineEvent;
use App\Listeners\Screens\NotifyAdminScreenAvailable;
use App\Listeners\Screens\NotifyAdminScreenOnline;
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
            \App\Listeners\Screens\ScreenStatusOffline::class,
            \App\Listeners\Screens\NotifyAdminScreenOffline::class,
        ],
        \App\Events\Screens\OnlineEvent::class => [
            \App\Listeners\Screens\ScreenStatusOnline::class,
            NotifyAdminScreenOnline::class,
        ],
        FirstPingEvent::class => [
            NotifyAdminScreenAvailable::class,
            ScreenStatusOnline::class
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
