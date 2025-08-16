<?php

namespace App\Filament\Resources\ScreenGroups;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;
use App\Filament\Resources\ScreenGroups\Pages\ListScreenGroups;
use App\Filament\Resources\ScreenGroups\Pages\CreateScreenGroup;
use App\Filament\Resources\ScreenGroups\Pages\EditScreenGroup;
use App\Filament\Resources\ScreenGroupResource\Pages;
use App\Models\ScreenGroup;
use App\Services\ScreenTabResource;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ScreenGroupResource extends Resource
{
    protected static ?string $model = ScreenGroup::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string | \UnitEnum | null $navigationGroup = 'Programming';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxValue(255)
                    ->columnSpanFull(),
                ScreenTabResource::getForm('settings.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListScreenGroups::route('/'),
            'create' => CreateScreenGroup::route('/create'),
            'edit' => EditScreenGroup::route('/{record}/edit'),
        ];
    }
}
