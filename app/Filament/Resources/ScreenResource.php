<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScreenResource\Pages;
use App\Models\Screen;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ScreenResource extends Resource
{
    protected static ?string $model = Screen::class;

    protected static ?string $navigationGroup = "Programming";

    protected static ?string $navigationIcon = 'heroicon-o-desktop-computer';
    protected static ?string $slug = 'screens';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('playlist_id')
                    ->relationship('playlist', 'name')
                    ->searchable()
                    ->required(),

                TextInput::make('name')
                    ->required(),

                DatePicker::make('last_ping_at')
                    ->label('Last Ping Date'),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?Screen $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?Screen $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SelectColumn::make('playlist_id')
                    ->label('Playlist')
                    ->disablePlaceholderSelection()
                    ->options(\App\Models\Playlist::pluck('name', 'id')->toArray()),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('last_ping_at')
                    ->label('Last Ping Date')
                    ->date(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScreens::route('/'),
            'create' => Pages\CreateScreen::route('/create'),
            'edit' => Pages\EditScreen::route('/{record}/edit'),
        ];
    }

    protected static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['playlist']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'playlist.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->playlist) {
            $details['Playlist'] = $record->playlist->name;
        }

        return $details;
    }
}
