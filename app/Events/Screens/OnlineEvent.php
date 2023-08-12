<?php

namespace App\Events\Screens;

use App\Models\Screen;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OnlineEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(readonly public Screen $screen)
    {
    }
}
