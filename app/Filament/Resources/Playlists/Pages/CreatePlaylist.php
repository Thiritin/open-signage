<?php

namespace App\Filament\Resources\Playlists\Pages;

use App\Filament\Resources\Playlists\PlaylistResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePlaylist extends CreateRecord
{
    protected static string $resource = PlaylistResource::class;
}
