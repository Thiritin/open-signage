<?php

namespace App\Filament\Resources\Screens\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Screens\ScreenResource;
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
