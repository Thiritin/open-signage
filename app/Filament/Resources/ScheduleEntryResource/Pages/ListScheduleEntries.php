<?php

namespace App\Filament\Resources\ScheduleEntryResource\Pages;

use App\Filament\Resources\ScheduleEntryResource;
use Filament\Pages\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListScheduleEntries extends ListRecords
{
    protected static string $resource = ScheduleEntryResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
