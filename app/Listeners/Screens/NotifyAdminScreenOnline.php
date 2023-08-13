<?php

namespace App\Listeners\Screens;

use App\Events\Broadcast\RefreshScreenEvent;
use App\Events\Screens\OfflineEvent;
use App\Events\Screens\OnlineEvent;
use App\Models\Screen;
use App\Models\User;
use App\Notifications\ScreenOfflineNotification;
use App\Notifications\ScreenOnlineNotification;
use Filament\Actions\Action;
use Filament\Notifications\Actions\ActionGroup;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;

class NotifyAdminScreenOnline implements ShouldQueue
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
    public function handle(OnlineEvent $event): void
    {
        if($event->screen->provisioned === false) {
            return;
        }
        \Illuminate\Support\Facades\Notification::route('telegram', config('services.telegram-bot-api.chat_id'))
            ->notify(new ScreenOnlineNotification($event->screen));
    }
}
