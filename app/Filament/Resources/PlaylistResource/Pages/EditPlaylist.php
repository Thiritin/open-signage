<?php

namespace App\Filament\Resources\PlaylistResource\Pages;

use App\Filament\Resources\PlaylistResource;
use Filament\Pages\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPlaylist extends EditRecord
{
    protected static string $resource = PlaylistResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
