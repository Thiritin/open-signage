<?php

namespace App\Filament\Resources\PlaylistResource\RelationManagers;

use App\Models\PlaylistItem;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\CreateAction;
use Filament\Pages\Actions\EditAction;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;

class PlaylistItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'playlistItems';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make([
                    TextInput::make('duration')
                        ->numeric()
                        ->minValue(1)
                        ->suffix('seconds')
                        ->columnSpan(2)
                        ->required(),

                    Select::make('page_id')
                        ->relationship('page', 'name')
                        ->columnSpan(5)
                        ->required(),

                    Select::make('layout_id')
                        ->relationship('layout', 'name')
                        ->columnSpan(5)
                        ->required(),
                ])->columns(12)->columnSpanFull(),
                TextInput::make('title')->columnSpanFull(),
                KeyValue::make('content')->columnSpanFull(),
            ]);
    }

    /**
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),

                TextColumn::make('playlist.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('page.name'),

                TextColumn::make('layout.name')
                    ->searchable()
                    ->sortable(),
            ])
            ->headerActions([
                \Filament\Tables\Actions\CreateAction::make()->modalWidth('7xl')
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make()->modalWidth('7xl'),
                DeleteAction::make()
            ])
            ->bulkActions([
                DeleteBulkAction::make()
            ]);
    }
}
