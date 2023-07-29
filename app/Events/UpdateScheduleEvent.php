<?php

namespace App\Events;

use App\Models\Announcement;
use App\Models\ScheduleEntry;
use App\Models\Screen;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateScheduleEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct()
    {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('ScreenAll')
        ];
    }

    public function broadcastAs()
    {
        return 'schedule.update';
    }

    public function broadcastWith()
    {
        return [
            'schedule' => ScheduleEntry::all()->toArray(),
        ];
    }
}
