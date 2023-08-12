<?php

namespace App\Listeners\Screens;

use App\Events\Broadcast\RefreshScreenEvent;
use App\Events\Screens\OfflineEvent;
use App\Events\Screens\OnlineEvent;
use App\Models\Screen;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Actions\ActionGroup;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;

class NotifyAdminScreenOnline
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
        $recipients = User::all();

        Notification::make()
            ->title('Screen is back online!')
            ->body("Screen {$event->screen->name} is back online!")
            ->success()
            ->icon('heroicon-o-power')
            ->actions([
                \Filament\Notifications\Actions\Action::make('View Screen')->url(route('filament.admin.resources.screens.edit',$event->screen))->link(),
            ])
            ->sendToDatabase($recipients);
    }
}
