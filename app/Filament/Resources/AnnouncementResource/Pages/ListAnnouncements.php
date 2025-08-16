<?php

namespace App\Filament\Resources\AnnouncementResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\AnnouncementResource;
use Filament\Resources\Pages\ListRecords;

class ListAnnouncements extends ListRecords
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
