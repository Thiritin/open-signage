<?php

namespace App\Notifications;

use App\Events\Broadcast\RefreshScreenEvent;
use App\Models\Screen;
use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use NotificationChannels\Telegram\TelegramMessage;

class ScreenOfflineNotification extends Notification
{
    public function __construct(readonly public Screen $screen)
    {
    }

    public function via(): array
    {
        $notifications = ['admin'];

        if (config('services.telegram-bot-api.chat_id'))
        {
            $notifications[] = 'telegram';
        }

        return $notifications;
    }

    /**
     * @throws \JsonException
     */
    public function toTelegram(): TelegramMessage
    {
        return TelegramMessage::create()
            ->to(config('services.telegram-bot-api.chat_id'))
            ->options(['parse_mode' => 'Markdown'])
            ->line("*Screen Offline: {$this->screen->name}*")
            ->line('')
            ->line('The screen did not send a heartbeat for 5 minutes. The system will attempt a browser refresh, after that a reboot if the screen is still offline.')
            ->line('')
            ->line('Please check the network connection of the screen.')
            ->line('')
            // Markdown link
            ->line(route('filament.admin.resources.screens.edit',$this->screen));
    }

    public function toAdmin(): void
    {
        $recipients = User::all();

        \Filament\Notifications\Notification::make()
            ->title('Screen offline')
            ->body("Screen {$this->screen->name} is offline.")
            ->warning()
            ->icon('heroicon-o-power')
            ->actions([
                \Filament\Notifications\Actions\Action::make('View Screen')->url(route('filament.admin.resources.screens.edit',$this->screen))->link(),
                \Filament\Notifications\Actions\Action::make('Refresh')
                    ->icon('heroicon-o-arrow-path')
                    ->label('Refresh')
                    ->color('warning')
                    ->action(fn () => broadcast(new RefreshScreenEvent($this->screen))),

                \Filament\Notifications\Actions\Action::make('Restart')
                    ->icon('heroicon-o-power')
                    ->label('Restart')
                    ->color('danger')
                    ->tooltip('Only works on kiosk managed screens.')
                    ->action(fn () => $this->screen->updateQuietly(['should_restart' => true])),
            ])
            ->sendToDatabase($recipients);
    }
}
