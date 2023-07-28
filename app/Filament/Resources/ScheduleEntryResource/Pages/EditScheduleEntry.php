<?php

namespace App\Filament\Resources\ScheduleEntryResource\Pages;

use App\Filament\Resources\ScheduleEntryResource;
use Filament\Pages\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditScheduleEntry extends EditRecord
{
    protected static string $resource = ScheduleEntryResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
