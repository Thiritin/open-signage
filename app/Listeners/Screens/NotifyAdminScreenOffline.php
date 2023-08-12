<?php

namespace App\Listeners\Screens;

use App\Events\Broadcast\RefreshScreenEvent;
use App\Events\Screens\OfflineEvent;
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

class NotifyAdminScreenOffline
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
    public function handle(OfflineEvent $event): void
    {
        $recipients = User::all();

        Notification::make()
            ->title('Screen offline')
            ->body("Screen {$event->screen->name} is offline.")
            ->warning()
            ->icon('heroicon-o-power')
            ->actions([
                \Filament\Notifications\Actions\Action::make('View Screen')->url(route('filament.admin.resources.screens.edit',$event->screen))->link(),
                \Filament\Notifications\Actions\Action::make('Refresh')
                    ->icon('heroicon-o-arrow-path')
                    ->label('Refresh')
                    ->color('warning')
                    ->action(fn () => broadcast(new RefreshScreenEvent($event->screen))),

                \Filament\Notifications\Actions\Action::make('Restart')
                    ->icon('heroicon-o-power')
                    ->label('Restart')
                    ->color('danger')
                    ->tooltip('Only works on kiosk managed screens.')
                    ->action(fn () => $event->screen->updateQuietly(['should_restart' => true])),
            ])
            ->sendToDatabase($recipients);
    }
}
