<?php

namespace App\Filament\Resources\ScreenGroupResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\ScreenGroupResource;
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
