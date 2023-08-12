<?php

namespace App\Listeners\Screens;

use App\Events\Screens\FirstPingEvent;
use App\Models\User;
use Filament\Notifications\Notification;

class NotifyAdminScreenAvailable
{
    public function __construct()
    {
    }

    public function handle(FirstPingEvent $event): void
    {
        $recipients = User::all();

        Notification::make()
            ->title('Screen has connected for the first time!')
            ->body("Screen {$event->screen->name} has connected for the first time and is ready for playlist assignment!")
            ->success()
            ->icon('heroicon-o-computer-desktop')
            ->actions([
                \Filament\Notifications\Actions\Action::make('View Screen')->url(route('filament.admin.resources.screens.edit',$event->screen))->link(),
            ])
            ->sendToDatabase($recipients);
    }
}
