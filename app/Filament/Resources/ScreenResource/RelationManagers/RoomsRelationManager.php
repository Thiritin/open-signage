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
                    ->reactive()
                    ->columnSpanFull()
                    ->maxLength(255),

                Forms\Components\DateTimePicker::make('starts_at'),

                Forms\Components\DateTimePicker::make('ends_at'),

                Forms\Components\CheckboxList::make('flags')
                    ->formatStateUsing(fn($state) => json_decode($state ?? "[]", true, 512, JSON_THROW_ON_ERROR))
                    ->options([
                        'first_aid' => 'First Aid',
                        'wheelchair' => 'Wheelchair Friendly',
                    ]),

                Forms\Components\Section::make('Icon')->columns()->schema([

                    Forms\Components\Select::make('icon')
//                        ->required()
                        ->reactive()
                        ->options(config('icons.icons')),

                    Forms\Components\TextInput::make('rotation')
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
                        ->visible(fn(Forms\Get $get) => in_array($get('icon'), config('icons.rotateable')))
                        ->default(0),

                    Forms\Components\Checkbox::make('mirror')
                        ->visible(fn(Forms\Get $get) => in_array($get('icon'), config('icons.mirrorable')))
                        ->default(false),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('pivot.icon')->label('Icon'),
                Tables\Columns\TextColumn::make('pivot.rotation')->label('Rotation'),
                Tables\Columns\IconColumn::make('pivot.mirror')->boolean()->label('Mirrored'),
                Tables\Columns\TextColumn::make('pivot.flags')->badge()->label('Flags'),
                Tables\Columns\IconColumn::make('pivot.primary')->boolean()->label('Primary'),
                Tables\Columns\TextColumn::make('pivot.starts_at')->dateTime()->label('Start Time'),
                Tables\Columns\TextColumn::make('pivot.ends_at')->dateTime()->label('End Time'),
            ])
            ->reorderable('sort')
            ->defaultSort('sort')
            ->paginated(false)
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }
}
