<?php

namespace App\Filament\Resources;

use App\Enums\EmergencyTypeEnum;
use App\Filament\Resources\RoomResource\RelationManagers\ScreensRelationManager;
use App\Filament\Resources\ScreenResource\Pages;
use App\Filament\Resources\ScreenResource\RelationManagers\RoomsRelationManager;
use App\Jobs\SetEmergencyPlaylistJob;
use App\Models\Screen;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Collection;

class ScreenResource extends Resource
{
    protected static ?string $model = Screen::class;

    protected static ?string $navigationGroup = "Programming";

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';
    protected static ?string $slug = 'screens';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('playlist_id')
                    ->relationship('playlist', 'name')
                    ->searchable(),

                TextInput::make('name')
                    ->hint('Only visible to admins and during screen identification.')
                    ->required(),

                Select::make('screen_group_id')
                    ->relationship('screenGroup', 'name'),

                TextInput::make('slug')
                    ->hint('This is the URL that will be used to access this screen.')
                    ->prefix(config('app.url').'/screens/')
                    ->required(),

                Select::make('orientation')->required()->selectablePlaceholder(false)->options([
                    'normal' => 'Normal',
                    'left' => 'Left',
                    'right' => 'Right',
                    'inverted' => 'Inverted',
                ]),

                Checkbox::make('provisioned')->helperText('If checked, this screen will be shown in the filter, this serves no function other than exluding possible randomly autoregistered screens.')->required(),

                Section::make('Network Settings')->columns(3)->description('Used for auto provisioning of thin clients.')->label('Network Settings')->schema([
                    TextInput::make('ip_address')
                        ->label('IP Address'),
                    TextInput::make('hostname')
                        ->label('Hostname'),
                    TextInput::make('mac_address')
                        ->label('MAC Address'),
                ]),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?Screen $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?Screen $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RoomsRelationManager::class,
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('screenGroup.name')->label('Screen Group'),

                SelectColumn::make('playlist_id')
                    ->label('Playlist')
                    ->disablePlaceholderSelection()
                    ->options(\App\Models\Playlist::pluck('name', 'id')->toArray()),

                TextInputColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('hostname')
                    ->searchable()
                    ->sortable(),

                CheckboxColumn::make('provisioned'),

                /*TextColumn::make('last_ping_at')
                    ->label('Last Ping Date')
                    ->date(),*/
            ])->filters([
                Filter::make('provisioned')->label('Show only provisioned screens')->query(fn(Builder $query
                ) => $query->where('provisioned', true))->default(true),
            ])->actions([
                \Filament\Tables\Actions\EditAction::make('Edit')
            ])->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('FireEmergencyAlert')
                        ->label('Fire Evacuation')
                        ->requiresConfirmation()
                        ->modalHeading('STOP! You are about to send a fire evacuation alert!')
                        ->modalDescription("You are about to send a fire evacuation alert! This will put the SELECTED screens in emergency mode and stop any currently playing content. This is reserved for emergencies only. Please confirm you want to send this alert.")
                        ->color('danger')
                        ->icon('heroicon-m-fire')
                        ->action(fn (Collection $records) => SetEmergencyPlaylistJob::dispatchSync(EmergencyTypeEnum::FIRE, $records))
                        ->form([
                            Checkbox::make('sensecheck')->required()->label('I am about to send an EMERGENCY ALERT!')->hintColor('danger')->hint('DANGER')->helperText('This is a serious action and should only be used in emergencies.'),
                        ]),
                    BulkAction::make('GeneralEmergencyAlert')
                        ->label('General Evacuation')
                        ->requiresConfirmation()
                        ->modalHeading('STOP! You are about to send an evacuation alert!')
                        ->modalDescription("You are about to send an emergency alert! This will put the SELECTED screens in emergency mode and stop any currently playing content. This is reserved for emergencies only. Please confirm you want to send this alert.")
                        ->form([
                            Checkbox::make('sensecheck')->required()->label('I am about to send an EMERGENCY ALERT!')->hintColor('danger')->hint('DANGER')->helperText('This is a serious action and should only be used in emergencies.'),
                        ])
                        ->color('danger')
                        ->icon('heroicon-s-arrow-right-on-rectangle')
                        ->action(fn (Collection $records) => SetEmergencyPlaylistJob::dispatchSync(EmergencyTypeEnum::EVACUATION, $records)),
                    BulkAction::make('CustomEmergencyAlert')
                        ->label('Custom Emergency Alert')
                        ->requiresConfirmation()
                        ->modalHeading('STOP! You are about to send an emergency alert!')
                        ->modalDescription("You are about to send an emergency alert! This will put the SELECTED screens in emergency mode and stop any currently playing content. This is reserved for emergencies only. Please confirm you want to send this alert.")
                        ->modalSubmitActionLabel('Confirm Send Alert')
                        ->action(fn (Collection $records, array $data) => SetEmergencyPlaylistJob::dispatchSync(EmergencyTypeEnum::CUSTOM, $records, $data['message'], $data['title']))
                        ->form([
                            TextInput::make('title')
                                ->label('Title')
                                ->required(),
                            Textarea::make('message')
                                ->label('Message')
                                ->required(),
                            Checkbox::make('sensecheck')->required()->label('I am about to send an EMERGENCY ALERT!')->hintColor('danger')->hint('DANGER')->helperText('This is a serious action and should only be used in emergencies.'),
                        ])
                        ->color('danger')
                        ->icon('heroicon-o-document-text'),
                    BulkAction::make('TestEmergencyAlert')
                        ->label('Test Emergency System')
                        ->requiresConfirmation()
                        ->modalHeading('STOP! You are about to send an emergency alert!')
                        ->modalDescription("You are about to send an emergency alert! This will put the SELECTED screens in emergency mode and stop any currently playing content. This is reserved for emergencies only. Please confirm you want to send this alert.")
                        ->modalSubmitActionLabel('Confirm Send Alert')
                        ->action(fn (Collection $records) => SetEmergencyPlaylistJob::dispatchSync(EmergencyTypeEnum::TEST, $records))
                        ->form([
                            Checkbox::make('sensecheck')->required()->label('I am about to send an TEST ALERT. ONLY USE THIS FOR TESTING, SCREENS WILL STILL JUMP INTO EMERGENCY MODE!')->hintColor('danger')->hint('DANGER')->helperText('This is a serious action and should only be used in emergencies.'),
                        ])
                        ->color('warning')
                        ->icon('heroicon-o-document-text'),
                    BulkAction::make('LiftEmergencyAlert')
                        ->label('Emergency Over Alert')
                        ->requiresConfirmation()
                        ->modalHeading('You are about to send an emergency over alert.')
                        ->modalDescription("This will display a message on the screens that the emergency is over.")
                        ->modalSubmitActionLabel('Danger is over, lift alert')
                        ->action(fn (Collection $records) => SetEmergencyPlaylistJob::dispatchSync(EmergencyTypeEnum::LIFTED, $records))
                        ->form([
                            Checkbox::make('sensecheck')->required()->label('There is no more danger, shows a lifted message on the screens.')->hintColor('danger')->hint('DANGER')->helperText('This is a serious action and should only be used in emergencies.'),
                        ])
                        ->color('warning')
                        ->icon('heroicon-o-document-text'),
                    BulkAction::make('ReturnRegularOperation')
                        ->label('Disable Emergency Mode')
                        ->requiresConfirmation()
                        ->modalHeading('You are about to disable the emergency mode.')
                        ->modalDescription("This will disable the emergency mode on the screens you selected and return them to normal operation.")
                        ->modalSubmitActionLabel('Confirm')
                        ->action(fn (Collection $records) => SetEmergencyPlaylistJob::dispatchSync(EmergencyTypeEnum::NONE, $records))
                        ->form([
                            Checkbox::make('sensecheck')->required()->label('Return screens to normal operation.')->hintColor('success')->hint('DANGER')->helperText('This is a serious action and should only be used in emergencies.'),
                        ])
                        ->color('success')
                        ->icon('heroicon-o-document-text'),
                ])
                    ->icon('heroicon-s-arrow-right-on-rectangle')
                    ->tooltip('Public Health and Safety Announcements, these are reserved only for emergencies.')
                    ->color('danger')
                    ->label('Emergency Alerts'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScreens::route('/'),
            'create' => Pages\CreateScreen::route('/create'),
            'edit' => Pages\EditScreen::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['playlist']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'playlist.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->playlist) {
            $details['Playlist'] = $record->playlist->name;
        }

        return $details;
    }
}
