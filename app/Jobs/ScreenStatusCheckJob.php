<?php

namespace App\Jobs;

use App\Enums\ScreenStatusEnum;
use App\Events\Screens\OfflineEvent;
use App\Models\Screen;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScreenStatusCheckJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle(): void
    {
        $screens = Screen::where('last_ping_at', '<', now()->subMinutes(2))
            ->where('status', '=', ScreenStatusEnum::ONLINE)
        ->get();
        $screens->each(fn($screen) => event(new OfflineEvent($screen)));
    }
}
