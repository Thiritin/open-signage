<?php

namespace App\Filament\Resources\ScheduleEntryResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\ScheduleEntryResource;
use Filament\Resources\Pages\ListRecords;

class ListScheduleEntries extends ListRecords
{
    protected static string $resource = ScheduleEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
