<?php

namespace App\Events;

use App\Models\Announcement;
use App\Services\ScreenDataGenerator;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateAnnouncementEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct()
    {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('ScreenAll'),
        ];
    }

    public function broadcastAs()
    {
        return 'announcement.update';
    }

    public function broadcastWith()
    {
        return [
            'announcements' => ScreenDataGenerator::announcements(),
        ];
    }
}
