<?php

namespace App\Listeners\Screens;

use App\Enums\ScreenStatusEnum;
use App\Events\Screens\FirstPingEvent;
use App\Events\Screens\OfflineEvent;
use App\Events\Screens\OnlineEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ScreenStatusOnline
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OnlineEvent|FirstPingEvent $event): void
    {
        $event->screen->updateQuietly([
            'status' => ScreenStatusEnum::ONLINE,
        ]);
    }
}
