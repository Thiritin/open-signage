<?php

namespace App\Filament\Resources\Screens\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('causer')
                    ->relationship('causer', 'name')
                    ->searchable()
                    ->columnSpanFull(),
                TextInput::make('description')->columnSpanFull(),
                KeyValue::make('properties.attributes')
                    ->columnSpan([
                        'default' => 2,
                        'sm' => 1,
                    ]),
                KeyValue::make('properties.old')
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
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('causer.name')
                    ->searchable(),
                TextColumn::make('description')
                    ->searchable(),
                TextColumn::make('log_name'),
                TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('event')
                    ->multiple()
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('logged_from')
                            ->label('Logged from'),
                        DatePicker::make('logged_until')
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
            ->recordActions([
                ViewAction::make()
            ])->emptyStateHeading('There have been no activities logged.');
    }
}
