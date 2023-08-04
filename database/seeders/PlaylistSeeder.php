<?php

namespace Database\Seeders;

use App\Enums\ResourceOwnership;
use App\Models\Layout;
use App\Models\Page;
use App\Models\Playlist;
use Illuminate\Database\Seeder;

class PlaylistSeeder extends Seeder
{
    public function run(): void
    {
        $playlist = Playlist::firstOrCreate([
            'name' => 'Screen Identification',
            'type' => ResourceOwnership::SYSTEM,
        ]);
        $playlist->playlistItems()->updateOrCreate([
            'page_id' => Page::where('component','ScreenIdentification')->first()->id,
            'layout_id' => Layout::where('component','None')->first()->id,
            'duration' => 10,
        ]);
    }
}
