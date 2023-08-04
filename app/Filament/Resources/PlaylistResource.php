<?php

namespace App\Filament\Resources;

use App\Enums\ResourceOwnership;
use App\Filament\Resources\PlaylistResource\Pages;
use App\Filament\Resources\PlaylistResource\RelationManagers\PlaylistItemsRelationManager;
use App\Models\Playlist;
use App\Settings\GeneralSettings;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Query\Builder;

class PlaylistResource extends Resource
{
    protected static ?string $model = Playlist::class;
    protected static ?string $navigationGroup = "Programming";
    protected static ?string $slug = 'playlists';

    protected static ?string $navigationIcon = 'heroicon-o-play';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?Playlist $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?Playlist $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')->badge()
                    ->formatStateUsing(fn($record) => ucfirst($record->type->value))
                    ->color(fn(ResourceOwnership $state) => match ($state->value) {
                        'emergency' => 'info',
                        'system' => 'gray',
                        'user' => 'success',
                    })

            ])->filters([
                SelectFilter::make('project')
                    ->relationship('project', 'name',fn($query) => $query->where('type','!=', ResourceOwnership::EMERGENCY))
                    ->default(app(GeneralSettings::class)->project_id)
            ])->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PlaylistItemsRelationManager::class,
        ];
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
        return ['name'];
    }
}
