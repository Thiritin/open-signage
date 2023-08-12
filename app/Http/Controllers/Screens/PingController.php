<?php

namespace App\Http\Controllers\Screens;

use App\Enums\ScreenStatusEnum;
use App\Events\Broadcast\RefreshScreenEvent;
use App\Events\Screens\FirstPingEvent;
use App\Events\Screens\OnlineEvent;
use App\Events\UpdateAnnouncementEvent;
use App\Events\UpdateScheduleEvent;
use App\Events\UpdateScreenPlaylistEvent;
use App\Http\Controllers\Controller;
use App\Models\Screen;

class PingController extends Controller
{
    public function __invoke(Screen $screen)
    {
        $screen->updateQuietly(['last_ping_at' => now()]); // Update the last_ping_at column to the current time

        if ($screen->status === ScreenStatusEnum::OFFLINE) {
            event(new OnlineEvent($screen)); // Fire the OnlineEvent event
            if($screen->should_restart) {
                $screen->updateQuietly(['should_restart' => false]);
            }
            broadcast(new RefreshScreenEvent($screen));
        }

        if ($screen->status === ScreenStatusEnum::UNINITIALIZED) {
            event(new FirstPingEvent($screen)); // Fire the OnlineEvent event
        }
    }
}
