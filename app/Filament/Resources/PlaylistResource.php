<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlaylistResource\Pages;
use App\Models\Playlist;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;

class PlaylistResource extends Resource
{
    protected static ?string $model = Playlist::class;

    protected static ?string $navigationGroup = "Programming";
    protected static ?string $slug = 'playlists';

    protected static ?string $navigationIcon = 'heroicon-o-play';
    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlaylists::route('/'),
            'create' => Pages\CreatePlaylist::route('/create'),
            'edit' => Pages\EditPlaylist::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
