<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleEntryResource\Pages;
use App\Models\ScheduleEntry;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;

class ScheduleEntryResource extends Resource
{
    protected static ?string $model = ScheduleEntry::class;
    protected static ?string $navigationGroup = "Content";

    protected static ?string $navigationIcon = 'heroicon-s-table';
    protected static ?string $slug = 'schedule-entries';

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
            'index' => Pages\ListScheduleEntries::route('/'),
            'create' => Pages\CreateScheduleEntry::route('/create'),
            'edit' => Pages\EditScheduleEntry::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
