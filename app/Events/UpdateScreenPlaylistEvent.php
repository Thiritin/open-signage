<?php

namespace App\Events;

use App\Models\PlaylistItem;
use App\Models\Scopes\HideEmergencyScope;
use App\Models\Screen;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateScreenPlaylistEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Screen $screen)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('Screen.'.$this->screen->id)
        ];
    }

    public function broadcastAs()
    {
        return 'page.update';
    }

    public function broadcastWith()
    {
        return [
            'pages' => $this->screen->playlist->playlistItems->map(fn(PlaylistItem $playlistItem) => [
                'layout' => [
                    'component' => $playlistItem->layout->component,
                    'path' => $playlistItem->layout->project->path,
                ],
                'path' => $playlistItem->page->project->path,
                'component' => $playlistItem->page->component,
                'props' => $playlistItem->content,
                'duration' => $playlistItem->duration,
                'title' => $playlistItem->title ?? '',
            ]),
            'screen' => $this->screen,
        ];
    }
}
