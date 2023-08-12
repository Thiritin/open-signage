<?php

namespace App\Filament\Resources\ScreenResource\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('causer')
                    ->relationship('causer', 'name')
                    ->searchable()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('description')->columnSpanFull(),
                Forms\Components\KeyValue::make('properties.attributes')
                    ->columnSpan([
                        'default' => 2,
                        'sm' => 1,
                    ]),
                Forms\Components\KeyValue::make('properties.old')
                    ->columnSpan([
                        'default' => 2,
                        'sm' => 1,
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('causer.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('log_name'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->multiple()
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('logged_from')
                            ->label('Logged from'),
                        Forms\Components\DatePicker::make('logged_until')
                            ->label('Logged until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['logged_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['logged_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['logged_from'] ?? null) {
                            $indicators['logged_from'] = 'Created from '.Carbon::parse($data['logged_from'])->toFormattedDateString();
                        }

                        if ($data['logged_until'] ?? null) {
                            $indicators['logged_until'] = 'Created until '.Carbon::parse($data['logged_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->poll(15)
            ->actions([
                Tables\Actions\ViewAction::make()
            ])->emptyStateHeading('There have been no activities logged.');
    }
}
