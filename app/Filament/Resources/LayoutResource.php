<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LayoutResource\Pages;
use App\Models\Layout;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;

class LayoutResource extends Resource
{
    protected static ?string $model = Layout::class;
    protected static ?string $navigationGroup = "Development";
    protected static ?string $slug = 'layouts';


    protected static ?string $navigationIcon = "heroicon-o-document";

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
            'index' => Pages\ListLayouts::route('/'),
            'create' => Pages\CreateLayout::route('/create'),
            'edit' => Pages\EditLayout::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
