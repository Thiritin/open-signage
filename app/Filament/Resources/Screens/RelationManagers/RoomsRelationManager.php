<?php

namespace App\Filament\Resources\Screens\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\AttachAction;
use Filament\Actions\EditAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RoomsRelationManager extends RelationManager
{
    protected static string $relationship = 'rooms';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->reactive()
                    ->columnSpanFull()
                    ->maxLength(255),

                DateTimePicker::make('starts_at'),

                DateTimePicker::make('ends_at'),

                CheckboxList::make('flags')
                    ->formatStateUsing(fn($state) => json_decode($state ?? "[]", true, 512, JSON_THROW_ON_ERROR))
                    ->options([
                        'first_aid' => 'First Aid',
                        'wheelchair' => 'Wheelchair Friendly',
                    ]),

                Section::make('Icon')->columns()->schema([

                    Select::make('icon')
//                        ->required()
                        ->reactive()
                        ->options(config('icons.icons')),

                    TextInput::make('rotation')
                        ->minValue(-180)
                        ->maxValue(180)
                        ->numeric()
                        ->required()
                        ->reactive()
                        ->datalist([
                            '-135',
                            '-90',
                            '-45',
                            '0',
                            '45',
                            '90',
                            '135',
                            '180'
                        ])
                        ->visible(fn(Get $get) => in_array($get('icon'), config('icons.rotateable')))
                        ->default(0),

                    Checkbox::make('mirror')
                        ->visible(fn(Get $get) => in_array($get('icon'), config('icons.mirrorable')))
                        ->default(false),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('pivot.icon')->label('Icon'),
                TextColumn::make('pivot.rotation')->label('Rotation'),
                IconColumn::make('pivot.mirror')->boolean()->label('Mirrored'),
                TextColumn::make('pivot.flags')->badge()->label('Flags'),
                IconColumn::make('pivot.primary')->boolean()->label('Primary'),
                TextColumn::make('pivot.starts_at')->dateTime()->label('Start Time'),
                TextColumn::make('pivot.ends_at')->dateTime()->label('End Time'),
            ])
            ->reorderable('sort')
            ->defaultSort('sort')
            ->paginated(false)
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DetachAction::make(),
            ])
            ->toolbarActions([
                DetachBulkAction::make(),
            ]);
    }
}
