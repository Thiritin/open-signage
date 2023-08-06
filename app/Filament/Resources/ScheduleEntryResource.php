<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleEntryResource\Pages;
use App\Models\ScheduleEntry;
use App\Settings\GeneralSettings;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ScheduleEntryResource extends Resource
{
    protected static ?string $model = ScheduleEntry::class;

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationIcon = 'heroicon-m-table-cells';

    protected static ?string $slug = 'schedule-entries';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->columnSpan(2)->columns()->schema([
                    Section::make('Meta')->schema([
                        TextInput::make('title')
                            ->required(),

                        Select::make('room_id')
                            ->relationship('room', 'name')
                            ->createOptionForm(fn (Form $form) => $form->schema([
                                TextInput::make('name')
                                    ->required(),
                            ]))
                            ->editOptionForm(fn (Form $form) => $form->schema([
                                TextInput::make('name')
                                    ->required(),
                            ])),

                        Select::make('schedule_type_id')
                            ->relationship('scheduleType', 'name')
                            ->createOptionForm(fn (Form $form) => $form->schema([
                                TextInput::make('name')
                                    ->required(),
                                ColorPicker::make('color')
                                    ->required(),
                            ]))
                            ->editOptionForm(fn (Form $form) => $form->schema([
                                TextInput::make('name')
                                    ->required(),
                                ColorPicker::make('color')
                                    ->required(),
                            ]))->helperText('Color is used for the background in the timetable.'),

                    ])->columnSpan(1),
                    Section::make('Event Time')->schema([
                        DateTimePicker::make('starts_at')
                            ->minutesStep(15)
                            ->hoursStep(1)
                            ->native(false)
                            ->minDate(app(GeneralSettings::class)->starts_at)
                            ->maxDate(app(GeneralSettings::class)->ends_at)
                            ->label('Starts Date'),

                        DateTimePicker::make('ends_at')
                            ->minutesStep(15)
                            ->hoursStep(1)
                            ->native(false)
                            ->minDate(app(GeneralSettings::class)->starts_at)
                            ->maxDate(app(GeneralSettings::class)->ends_at)
                            ->label('Ends Date'),
                    ])->columnSpan(1),
                ]),
                Group::make()->columnSpan(1)->columns(1)->schema([
                    Section::make('Flags')->schema([
                        CheckboxList::make('flags')
                            ->options([
                                'moved' => 'Moved',
                                'cancelled' => 'Cancelled',
                                'after_dark' => 'After Dark',
                            ]),

                        TextInput::make('delay')
                            ->default(0)
                            ->numeric()
                            ->suffix('minutes')
                            ->hint('Use in combination with delay'),

                        Textarea::make('message')
                            ->helperText('Use in combination with delay (will be displayed as delay reason) or cancelled (as cancel reason).'),

                    ])->columnSpan(1),

                    Placeholder::make('created_at')
                        ->label('Created Date')
                        ->content(fn (
                            ?ScheduleEntry $record
                        ): string => $record?->created_at?->diffForHumans() ?? '-'),

                    Placeholder::make('updated_at')
                        ->label('Last Modified Date')
                        ->content(fn (
                            ?ScheduleEntry $record
                        ): string => $record?->updated_at?->diffForHumans() ?? '-'),
                ]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('room.name'),

                TextColumn::make('starts_at')
                    ->label('Starts')
                    ->dateTime(),

                TextColumn::make('ends_at')
                    ->label('Ends')
                    ->dateTime(),

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScheduleEntries::route('/'),
            'create' => Pages\CreateScheduleEntry::route('/create'),
            'edit' => Pages\EditScheduleEntry::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title'];
    }
}
