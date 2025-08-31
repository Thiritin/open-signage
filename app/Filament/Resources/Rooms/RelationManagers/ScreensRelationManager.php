<?php

namespace App\Filament\Resources\Rooms\RelationManagers;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use App\Models\Screen;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ScreensRelationManager extends RelationManager
{
    protected static string $relationship = 'screens';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('playlist_id')
                    ->relationship('playlist', 'name')
                    ->searchable(),

                TextInput::make('name')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                DatePicker::make('last_ping_at')
                    ->label('Last Ping Date'),

                TextEntry::make('created_at')
                    ->label('Created Date')
                    ->state(fn (?Screen $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                TextEntry::make('updated_at')
                    ->label('Last Modified Date')
                    ->state(fn (?Screen $record): string => $record?->updated_at?->diffForHumans() ?? '-'),

                TextInput::make('slug')
                    ->disabled()
                    ->required()
                    ->unique(Screen::class, 'slug', fn ($record) => $record),

                Checkbox::make('provisioned'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('playlist.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('last_ping_at')
                    ->label('Last Ping Date')
                    ->date(),

                TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('provisioned'),
            ]);
    }
}
