<?php

namespace App\Filament\Resources\RoomResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\RoomResource;
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
