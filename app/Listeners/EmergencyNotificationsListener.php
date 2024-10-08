<?php

namespace App\Listeners;

use App\Events\EmergencyEvent;
use App\Notifications\EmergencyNotification;
use App\Notifications\ScreenFirstTimeNotification;

class EmergencyNotificationsListener
{
    public function __construct()
    {
    }

    public function handle(EmergencyEvent $event): void
    {
        if (config('services.telegram-bot-api.chat_id'))
        {
            \Illuminate\Support\Facades\Notification::route('telegram', config('services.telegram-bot-api.chat_id'))
                ->notify(new EmergencyNotification($event->user, $event->type, $event->screens));
        }
    }
}
