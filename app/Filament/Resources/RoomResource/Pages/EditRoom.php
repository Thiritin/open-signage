<?php

namespace App\Filament\Resources\RoomResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\RoomResource;
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
