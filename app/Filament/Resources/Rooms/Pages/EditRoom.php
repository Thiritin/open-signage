<?php

namespace App\Filament\Resources\Rooms\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Rooms\RoomResource;
use Filament\Resources\Pages\EditRecord;

class EditRoom extends EditRecord
{
    protected static string $resource = RoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
