<?php

namespace App\Filament\Resources\AnnouncementResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\AnnouncementResource;
use Filament\Resources\Pages\EditRecord;

class EditAnnouncement extends EditRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
