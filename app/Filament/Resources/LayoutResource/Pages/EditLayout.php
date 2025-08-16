<?php

namespace App\Filament\Resources\LayoutResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\LayoutResource;
use Filament\Resources\Pages\EditRecord;

class EditLayout extends EditRecord
{
    protected static string $resource = LayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
