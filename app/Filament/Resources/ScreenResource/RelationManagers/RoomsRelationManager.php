<?php

namespace App\Filament\Resources\ScreenResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RoomsRelationManager extends RelationManager
{
    protected static string $relationship = 'rooms';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('direction')
                    ->required()
                    ->options([
                        'left' => 'Left',
                        'right' => 'Right',
                        'top' => 'Top',
                        'bottom' => 'Bottom',
                    ]),
                Forms\Components\Checkbox::make('primary'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('pivot.direction')->label('Direction'),
                Tables\Columns\IconColumn::make('pivot.primary')->boolean()->label('Primary'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::class::make(),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
