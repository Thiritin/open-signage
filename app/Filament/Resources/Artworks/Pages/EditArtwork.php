<?php

namespace App\Filament\Resources\Artworks\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Artworks\ArtworkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArtwork extends EditRecord
{
    protected static string $resource = ArtworkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
