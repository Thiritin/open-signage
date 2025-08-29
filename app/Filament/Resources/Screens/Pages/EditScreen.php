<?php

namespace App\Filament\Resources\Screens\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Screens\ScreenResource;
use Filament\Resources\Pages\EditRecord;

class EditScreen extends EditRecord
{
    protected static string $resource = ScreenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
