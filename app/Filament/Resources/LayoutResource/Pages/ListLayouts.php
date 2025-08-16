<?php

namespace App\Filament\Resources\LayoutResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\LayoutResource;
use Filament\Resources\Pages\ListRecords;

class ListLayouts extends ListRecords
{
    protected static string $resource = LayoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
