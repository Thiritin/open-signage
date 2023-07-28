<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScreenResource\Pages;
use App\Models\Screen;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Livewire\Features\Placeholder;

class ScreenResource extends Resource
{
    protected static ?string $model = Screen::class;
    protected static ?string $navigationGroup = "Programming";

    protected static ?string $navigationIcon = 'heroicon-o-desktop-computer';
    protected static ?string $slug = 'screens';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Placeholder::make('id')->hiddenOn('create'),
                TextInput::make('name')->required(),
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
            'index' => Pages\ListScreens::route('/'),
            'create' => Pages\CreateScreen::route('/create'),
            'edit' => Pages\EditScreen::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
