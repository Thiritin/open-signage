<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleEntryResource\Pages;
use App\Models\ScheduleEntry;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;

class ScheduleEntryResource extends Resource
{
    protected static ?string $model = ScheduleEntry::class;

    protected static ?string $navigationGroup = "Content";

    protected static ?string $navigationIcon = 'heroicon-s-table';
    protected static ?string $slug = 'schedule-entries';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required(),

                TextInput::make('room'),

                DateTimePicker::make('starts_at')
                    ->label('Starts Date'),

                DateTimePicker::make('ends_at')
                    ->label('Ends Date'),

                Checkbox::make('is_moved'),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?ScheduleEntry $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?ScheduleEntry $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('room'),

                TextColumn::make('starts_at')
                    ->label('Starts Date')
                    ->date(),

                TextColumn::make('ends_at')
                    ->label('Ends Date')
                    ->date(),

                TextColumn::make('is_moved'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScheduleEntries::route('/'),
            'create' => Pages\CreateScheduleEntry::route('/create'),
            'edit' => Pages\EditScheduleEntry::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title'];
    }
}
