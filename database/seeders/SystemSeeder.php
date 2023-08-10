<?php

namespace Database\Seeders;

use App\Enums\ResourceOwnership;
use App\Models\Page;
use App\Models\Project;
use App\Settings\GeneralSettings;
use Illuminate\Database\Seeder;

class SystemSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::updateOrCreate([
            'path' => 'system',
        ], [
            'name' => 'System',
            'path' => 'System',
            'type' => ResourceOwnership::SYSTEM,
        ]);

        $pageScreenIdent = $project->pages()->updateOrCreate([
            'component' => 'ScreenIdentification',
        ], [
            'name' => 'Screen Identification',
            'component' => 'ScreenIdentification',
        ]);

        $layoutScreenIdent = $project->layouts()->updateOrCreate([
            'component' => 'None',
        ], [
            'name' => 'None',
            'component' => 'None',
        ]);

        $playlist = $project->playlists()->firstOrCreate([
            'name' => 'Screen Identification',
        ]);
        $playlist->playlistItems()->updateOrCreate([
            'page_id' => $pageScreenIdent->id,
            'layout_id' => $layoutScreenIdent->id,
        ], [
            'duration' => 0,
        ]);

        $settings = app(GeneralSettings::class);
        if (empty($settings->playlist_id)) {
            $settings->playlist_id = $playlist->id;
            $settings->save();
        }

        /**
         * Artwork Autoplay System Page
         */
        $artworkPage = $project->pages()->updateOrCreate([
            'component' => 'ArtworkAutoplay',
        ], [
            'name' => 'Artwork Autoplay',
            'component' => 'ArtworkAutoplay',
            'schema' => [
                [
                    'name' => 'Show Duration (ms)',
                    'property' => 'playSpeed',
                    'type' => 'TextInput',
                ],
                [
                    'name' => 'Transition Duration (ms)',
                    'property' => 'transition',
                    'type' => 'TextInput',
                ],
            ],
        ]);

        $fullScreenFlv = $project->pages()->updateOrCreate([
            'component' => 'FullScreenFlv',
        ], [
            'name' => 'Full Screen Flv',
            'component' => 'FullScreenFlv',
            'schema' => [
                [
                    'name' => 'Stream URL (FLV)',
                    'property' => 'streamUrl',
                    'type' => 'TextInput',
                ],
            ],
        ]);
    }
}
