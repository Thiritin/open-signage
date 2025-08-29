<?php

namespace App\Filament\Resources\ScheduleEntries\Pages;

use App\Filament\Resources\ScheduleEntries\ScheduleEntryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateScheduleEntry extends CreateRecord
{
    protected static string $resource = ScheduleEntryResource::class;
}
