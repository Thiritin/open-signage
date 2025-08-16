<?php

namespace App\Filament\Resources\Rooms\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Rooms\RoomResource;
use Filament\Resources\Pages\ListRecords;

class ListRooms extends ListRecords
{
    protected static string $resource = RoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
