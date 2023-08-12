<?php

namespace App\Channels;

use Illuminate\Support\Facades\Notification;

class AdminChannel
{
    public function send($notifiable, \Illuminate\Notifications\Notification $notification): bool
    {
        // Notifications are handled in the routing method of the notification
        $notification->toAdmin();
        return true;
    }
}
