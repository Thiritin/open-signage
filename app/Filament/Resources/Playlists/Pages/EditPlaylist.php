<?php

namespace App\Filament\Resources\Playlists\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Playlists\PlaylistResource;
use Filament\Resources\Pages\EditRecord;

class EditPlaylist extends EditRecord
{
    protected static string $resource = PlaylistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
