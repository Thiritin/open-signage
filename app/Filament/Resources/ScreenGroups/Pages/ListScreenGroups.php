<?php

namespace App\Filament\Resources\ScreenGroups\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\ScreenGroups\ScreenGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScreenGroups extends ListRecords
{
    protected static string $resource = ScreenGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
