<?php

namespace App\Events\Screens;

use App\Models\Screen;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OnlineEvent implements ShouldQueue
{
    use Dispatchable, SerializesModels;

    public function __construct(readonly public Screen $screen)
    {
    }
}
