<?php

namespace App\Notifications;

use App\Enums\EmergencyTypeEnum;
use App\Events\Broadcast\RefreshScreenEvent;
use App\Models\Screen;
use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use NotificationChannels\Telegram\TelegramMessage;

class EmergencyNotification extends Notification
{
    public function __construct(
        public readonly User $user,
        public readonly EmergencyTypeEnum $type,
        public readonly Collection $screens
    )
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
        $message = TelegramMessage::create()
            ->to(config('services.telegram-bot-api.chat_id'))
            ->options(['parse_mode' => 'Markdown'])
            ->line("*Emergency Mode! {$this->type->name}*")
            ->line('')
            ->line('Emergency Mode has been activated for the following screens:')
            ->line('');

        $this->screens->each(fn(Screen $screen) => $message = $message->line("- {$screen->name}"));

        return $message;
    }

    public function toAdmin(): void
    {
        $recipients = User::all();

        \Filament\Notifications\Notification::make()
            ->title('Emergency Mode was activated!')
            ->body("Multiple screen we're put into emergency mode!")
            ->danger()
            ->icon('heroicon-m-fire')
            ->sendToDatabase($recipients);
    }
}
