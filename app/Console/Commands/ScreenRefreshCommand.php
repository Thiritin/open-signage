<?php

namespace App\Console\Commands;

use App\Events\Broadcast\RefreshScreenEvent;
use App\Models\Screen;
use Illuminate\Console\Command;

class ScreenRefreshCommand extends Command
{
    protected $signature = 'screen:refresh-all';

    protected $description = 'Sends a refresh command to all screens';

    public function handle(): void
    {
        Screen::all()->each(fn(Screen $screen) => broadcast(new RefreshScreenEvent($screen)));
    }
}
