<?php

namespace App\Filament\Resources\LayoutResource\Pages;

use App\Filament\Resources\LayoutResource;
use Filament\Pages\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLayout extends EditRecord
{
    protected static string $resource = LayoutResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
