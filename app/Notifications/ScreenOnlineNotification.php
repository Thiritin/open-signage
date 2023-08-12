<?php

namespace App\Notifications;

use App\Events\Broadcast\RefreshScreenEvent;
use App\Models\Screen;
use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use NotificationChannels\Telegram\TelegramMessage;

class ScreenOnlineNotification extends Notification
{
    public function __construct(readonly private Screen $screen)
    {
    }

    public function via(): array
    {
        return ['telegram','admin'];
    }

    /**
     * @throws \JsonException
     */
    public function toTelegram(): TelegramMessage
    {
        return TelegramMessage::create()
            ->to(config('services.telegram-bot-api.chat_id'))
            ->options(['parse_mode' => 'Markdown'])
            ->line("*Screen Online: {$this->screen->name}*")
            ->line('')
            ->line("Screen {$this->screen->name} is back online!")
            ->line('')
            // Markdown link
            ->line(route('filament.admin.resources.screens.edit',$this->screen));
    }

    public function toAdmin(): void
    {
        $recipients = User::all();

        \Filament\Notifications\Notification::make()
            ->title('Screen is back online!')
            ->body("Screen {$this->screen->name} is back online!")
            ->success()
            ->icon('heroicon-o-power')
            ->actions([
                \Filament\Notifications\Actions\Action::make('View Screen')->url(route('filament.admin.resources.screens.edit',$this->screen))->link(),
            ])
            ->sendToDatabase($recipients);
    }
}
