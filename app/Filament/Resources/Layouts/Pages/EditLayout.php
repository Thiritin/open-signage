<?php

namespace App\Filament\Resources\Layouts\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Layouts\LayoutResource;
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
