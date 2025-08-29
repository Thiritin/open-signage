<?php

namespace App\Filament\Resources\Artworks\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Artworks\ArtworkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListArtworks extends ListRecords
{
    protected static string $resource = ArtworkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
