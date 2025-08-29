<?php

namespace App\Filament\Resources\ScreenGroups\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\ScreenGroups\ScreenGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScreenGroup extends EditRecord
{
    protected static string $resource = ScreenGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
