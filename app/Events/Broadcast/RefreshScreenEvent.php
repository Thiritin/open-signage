<?php

namespace App\Events\Broadcast;

use App\Models\Screen;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RefreshScreenEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Screen $screen)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('Screen.' . $this->screen->id),
        ];
    }

    public function broadcastAs()
    {
        return 'screen.refresh';
    }
}
