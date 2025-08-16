<?php

namespace App\Filament\Resources\ScheduleEntries\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\ScheduleEntries\ScheduleEntryResource;
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
