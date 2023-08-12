<?php

namespace App\Listeners\Screens;

use App\Events\Screens\FirstPingEvent;
use App\Models\User;
use App\Notifications\ScreenFirstTimeNotification;
use App\Notifications\ScreenOfflineNotification;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyAdminScreenAvailable implements ShouldQueue
{
    public function __construct()
    {
    }

    public function handle(FirstPingEvent $event): void
    {
        \Illuminate\Support\Facades\Notification::route('telegram', config('services.telegram-bot-api.chat_id'))
            ->notify(new ScreenFirstTimeNotification($event->screen));
    }
}
