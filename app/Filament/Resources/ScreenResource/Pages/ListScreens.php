<?php

namespace App\Filament\Resources\ScreenResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\ScreenResource;
use Filament\Resources\Pages\ListRecords;

class ListScreens extends ListRecords
{
    protected static string $resource = ScreenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
