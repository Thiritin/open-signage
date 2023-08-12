<?php

namespace App\Events\Screens;

use App\Models\Screen;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OfflineEvent implements ShouldQueue
{
    use Dispatchable, SerializesModels;

    public function __construct(readonly public Screen $screen)
    {
    }
}
