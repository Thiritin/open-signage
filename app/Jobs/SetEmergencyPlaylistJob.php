<?php

namespace App\Jobs;

use App\Enums\EmergencyTypeEnum;
use App\Enums\ResourceOwnership;
use App\Models\Playlist;
use App\Models\Project;
use App\Models\Scopes\HideEmergencyScope;
use App\Models\Screen;
use App\Settings\GeneralSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class SetEmergencyPlaylistJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly EmergencyTypeEnum $type,
        private readonly Collection $screens,
        private readonly ?string $message = null,
        private readonly ?string $title = null
    ) {

    }

    public function handle(): void
    {
        $project = Project::firstWhere('type', ResourceOwnership::EMERGENCY);
        $playList = $project->playlists()->create([
            'name' => 'Emergency ' . ucfirst(strtolower($this->type->name)) . ' Playlist ' . now()->format('Y-m-d H:i:s'),
        ]);

        if ($this->type === EmergencyTypeEnum::NONE) {
            // Set Screen to default Playlist
            $this->screens->each(fn (Screen $screen) => $screen->update([
                'playlist_id' => $screen->default_playlist_id ?? app(GeneralSettings::class)->playlist_id,
            ]));
            // Cleanup Emergency Playlist
            Playlist::whereHas('project', fn ($q) => $q->where('type', ResourceOwnership::EMERGENCY))
                ->whereDoesntHave('screens', fn ($query) => $query->withoutGlobalScope(HideEmergencyScope::class))->delete();

            return;
        }

        if ($this->type === EmergencyTypeEnum::CUSTOM) {
            $title = $this->title;
            $message = $this->message;

            $playList->playlistItems()->create([
                'page_id' => $project->pages()->firstWhere('component', 'EmergencyAlert')->id,
                'layout_id' => $project->layouts()->firstWhere('component', 'EmergencyLayout')->id,
                'duration' => 0,
                'content' => [
                    'type' => $this->type->name,
                    'title' => $title,
                    'message' => $message,
                ],
            ]);

        } else {
            $playList->playlistItems()->create([
                'page_id' => $project->pages()->firstWhere('component', 'EmergencyAlert')->id,
                'layout_id' => $project->layouts()->firstWhere('component', 'EmergencyLayout')->id,
                'duration' => 5,
                'content' => [
                    'type' => $this->type->name,
                    'title' => trans('emergency.' . $this->type->name . '.title', [], 'en'),
                    'message' => trans('emergency.' . $this->type->name . '.message', [], 'en'),
                ],
            ]);

            $playList->playlistItems()->create([
                'page_id' => $project->pages()->firstWhere('component', 'EmergencyAlert')->id,
                'layout_id' => $project->layouts()->firstWhere('component', 'EmergencyLayout')->id,
                'duration' => 5,
                'content' => [
                    'type' => $this->type->name,
                    'title' => trans('emergency.' . $this->type->name . '.title', [], 'de'),
                    'message' => trans('emergency.' . $this->type->name . '.message', [], 'de'),
                ],
            ]);
        }

        $this->screens->each(fn (Screen $screen) => $screen->update([
            'default_playlist_id' => $screen->default_playlist_id ?? $screen->playlist_id,
            'playlist_id' => $playList->id,
        ]));
    }
}
