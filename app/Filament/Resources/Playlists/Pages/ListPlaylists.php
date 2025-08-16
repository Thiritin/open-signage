<?php

namespace App\Filament\Resources\Playlists\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Playlists\PlaylistResource;
use Filament\Resources\Pages\ListRecords;

class ListPlaylists extends ListRecords
{
    protected static string $resource = PlaylistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
