<?php

namespace App\Listeners;

use App\Events\EmergencyEvent;

class LogEmergencyListener
{
    public function __construct()
    {
    }

    public function handle(EmergencyEvent $event): void
    {
        activity()
            ->causedBy($event->user)
            ->withProperties([
                'type' => $event->type->name,
                'screens' => $event->screens->toArray(),
            ])
            ->event('emergency');

    }
}
