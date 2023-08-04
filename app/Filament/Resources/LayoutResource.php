<?php

namespace App\Filament\Resources;

use App\Enums\ResourceOwnership;
use App\Filament\Resources\LayoutResource\Pages;
use App\Models\Layout;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class LayoutResource extends Resource
{
    protected static ?string $model = Layout::class;
    protected static ?string $navigationGroup = "Development";

    protected static ?string $slug = 'layouts';
    protected static ?string $navigationIcon = "heroicon-o-document";
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),

                TextInput::make('component')
                    ->required(),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?Layout $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?Layout $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('component'),

                TextColumn::make('type')->badge()
                    ->formatStateUsing(fn($record) => ucfirst($record->type->value))
                    ->color(fn (ResourceOwnership $state) => match ($state->value) {
                        'emergency' => 'info',
                        'system' => 'gray',
                        'user' => 'success',
                    })

            ])->actions([
                EditAction::make(),
                DeleteAction::make(),
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
        return ['name'];
    }
}
