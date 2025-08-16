<?php

namespace App\Filament\Resources\ScheduleEntryResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\ScheduleEntryResource;
use Filament\Resources\Pages\EditRecord;

class EditScheduleEntry extends EditRecord
{
    protected static string $resource = ScheduleEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
