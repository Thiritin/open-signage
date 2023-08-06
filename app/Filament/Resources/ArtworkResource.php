<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArtworkResource\Pages;
use App\Models\Artwork;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ArtworkResource extends Resource
{
    protected static ?string $model = Artwork::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('artist'),
                Forms\Components\FileUpload::make('file_horizontal')
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatios(['16:9', null])
                    ->imageEditorMode(2),
                Forms\Components\FileUpload::make('file_vertical')
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatios(['9:16', null])
                    ->imageEditorMode(2),
                Forms\Components\FileUpload::make('file_banner')
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatios(['5:2', null])
                    ->imageEditorMode(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('artist'),
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
            'index' => Pages\ListArtworks::route('/'),
            'create' => Pages\CreateArtwork::route('/create'),
            'edit' => Pages\EditArtwork::route('/{record}/edit'),
        ];
    }
}
