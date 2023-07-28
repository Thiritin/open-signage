<?php

namespace App\Filament\Resources\ScreenResource\Pages;

use App\Filament\Resources\ScreenResource;
use Filament\Pages\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListScreens extends ListRecords
{
    protected static string $resource = ScreenResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
