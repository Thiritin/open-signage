<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScreenGroupResource\Pages;
use App\Filament\Resources\ScreenGroupResource\RelationManagers;
use App\Services\ScreenTabResource;
use DateTimeZone;
use App\Models\ScreenGroup;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ScreenGroupResource extends Resource
{
    protected static ?string $model = ScreenGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationGroup = "Programming";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxValue(255)
                    ->columnSpanFull(),
                ScreenTabResource::getForm("settings."),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListScreenGroups::route('/'),
            'create' => Pages\CreateScreenGroup::route('/create'),
            'edit' => Pages\EditScreenGroup::route('/{record}/edit'),
        ];
    }
}
