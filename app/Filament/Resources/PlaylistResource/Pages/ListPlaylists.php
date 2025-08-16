<?php

namespace App\Filament\Resources\PlaylistResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\PlaylistResource;
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
