<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Filament\Resources\RoomResource\RelationManagers\ScreensRelationManager;
use App\Models\Room;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $slug = 'rooms';

    protected static ?string $navigationGroup = 'Programming';

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),

                TextInput::make('venue_name'),
                TextInput::make('external_name')
                    ->helperText('Name from any external system.')
                    ->hint('Do not modify if you don\'t know what you are doing.'),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn (?Room $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn (?Room $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('venue_name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('external_name')
                    ->searchable()
                    ->sortable(),
            ])->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])->bulkActions([
                DeleteBulkAction::make()
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ScreensRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
