<?php

namespace App\Filament\Resources\Screens;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\TextSize;
use App\Models\Playlist;
use Filament\Actions\EditAction;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Auth;
use App\Filament\Resources\Screens\Pages\ListScreens;
use App\Filament\Resources\Screens\Pages\CreateScreen;
use App\Filament\Resources\Screens\Pages\EditScreen;
use App\Enums\EmergencyTypeEnum;
use App\Enums\ResourceOwnership;
use App\Enums\ScreenStatusEnum;
use App\Events\Broadcast\RefreshScreenEvent;
use App\Filament\Resources\ScreenResource\Pages;
use App\Filament\Resources\Screens\RelationManagers\ActivitiesRelationManager;
use App\Filament\Resources\Screens\RelationManagers\RoomsRelationManager;
use App\Jobs\SetEmergencyPlaylistJob;
use App\Models\Screen;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;

class ScreenResource extends Resource
{
    protected static ?string $model = Screen::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Programming';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-computer-desktop';

    protected static ?string $slug = 'screens';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('playlist_id')
                    ->relationship('playlist', 'name', fn ($query) => $query->normal())
                    ->preload()
                    ->searchable()
                    ->required(),

                TextInput::make('name')
                    ->hint('Only visible to admins and during screen identification.')
                    ->required(),

                Select::make('screen_group_id')
                    ->relationship('screenGroup', 'name'),

                Select::make('room_id')
                    ->relationship('room', 'name')
                    ->preload()
                    ->label('Primary Room')
                    ->hint('Will be used to display room related information on the screen.')
                    ->nullable(),

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

                Checkbox::make('provisioned')->helperText('If checked, this screen will be shown in the filter, this serves no function other than exluding possible randomly autoregistered screens.'),
                Checkbox::make('should_restart')->helperText('If checked, kiosk managed screens will restart once and this checkbox will be unset.'),

                Section::make('Network Settings')->columns(3)->description('Used for auto provisioning of thin clients.')->label('Network Settings')->schema([
                    TextInput::make('ip_address')
                        ->label('IP Address'),
                    TextInput::make('hostname')
                        ->label('Hostname'),
                    TextInput::make('mac_address')
                        ->label('MAC Address'),
                ]),

                TextEntry::make('created_at')
                    ->label('Created Date')
                    ->state(fn(?Screen $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                TextEntry::make('updated_at')
                    ->label('Last Modified Date')
                    ->state(fn(?Screen $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ActivitiesRelationManager::class,
            RoomsRelationManager::class,
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->alignStart()
                    ->color(fn($state) => match ($state->value) {
                        ScreenStatusEnum::UNINITIALIZED->value => 'gray',
                        ScreenStatusEnum::OFFLINE->value => 'danger',
                        ScreenStatusEnum::ONLINE->value => 'success',
                    })
                    ->formatStateUsing(fn($state) => ucfirst($state->value))
                    ->sortable(),

                TextColumn::make('mode')
                    ->badge()
                    ->visible(fn() => Screen::whereHas('playlist.project', fn(Builder $query) => $query->where('type','=',ResourceOwnership::EMERGENCY))->exists())
                    ->alignStart()
                    ->size(TextSize::Large)
                    ->state(fn(Screen $screen) => $screen->isEmergency() ? $screen->playlist->name : 'Normal')
                    ->label('Mode')
                    ->color(fn(Screen $screen) => $screen->isEmergency() ? 'danger' : 'success')
                    ->icon(fn(Screen $screen
                    ) => $screen->isEmergency() ? 'heroicon-o-exclamation-circle' : 'heroicon-o-check-circle'),

                TextColumn::make('room.name')
                    ->sortable()
                    ->searchable()
                    ->label('Room'),

                SelectColumn::make('playlist_id')
                    ->label('Playlist')
                    ->selectablePlaceholder(false)
                    ->disabled(fn (Screen $screen) => $screen->isEmergency())
                    ->options(fn (Screen $screen) => Playlist::query()
                        ->whereHas('playlistItems')
                        ->normal()
                        ->when($screen?->playlist_id, fn ($query) => $query->orWhere('id', $screen->playlist_id))
                        ->pluck('name', 'id')
                        ->toArray()),

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

                TextColumn::make('last_ping_at')
                    ->label('Last Ping')
                    ->sortable()
                    ->state(fn(?Screen $record): string => $record?->last_ping_at?->diffForHumans() ?? '-'),

            ])->filters([
                Filter::make('provisioned')->label('Show only provisioned screens')->query(fn(
                    Builder $query
                ) => $query->where('provisioned', true))->default(true),
                SelectFilter::make('room')->relationship('room', 'name')->multiple()
            ])->recordActions([
                EditAction::make('Edit'),
            ])
            ->poll()
            ->striped()
            ->toolbarActions(array(
                BulkAction::make('Refresh')
                    ->icon('heroicon-o-arrow-path')
                    ->label('Refresh')
                    ->action(fn(
                        Collection $records
                    ) => $records->each(fn(Screen $screen) => broadcast(new RefreshScreenEvent($screen)))),

                BulkAction::make('Restart')
                    ->icon('heroicon-o-power')
                    ->label('Restart')
                    ->tooltip('Only works on kiosk managed screens.')
                    ->action(fn(
                        Collection $records
                    ) => $records->each(fn(Screen $screen) => $screen->updateQuietly(array('should_restart' => true)))),

                BulkAction::make('Set Playlist')
                    ->icon('heroicon-o-play')
                    ->label('Set Playlist')
                    ->schema(array(
                        Select::make('playlist_id')
                            ->options(Playlist::whereHas('playlistItems')
                                ->normal()
                                ->pluck('name', 'id')->toArray())
                            ->required()
                    ))
                    ->action(function (
                        Collection $records
                    ,$data) {
                        $records->each(fn(Screen $screen) => $screen->update(array('playlist_id' => $data['playlist_id'])));
                        return Redirect::route('filament.admin.resources.screens.index');
                    }),

                DeleteBulkAction::make(),

                BulkActionGroup::make(array(
                    BulkAction::make('FireEmergencyAlert')
                        ->label('Fire Evacuation')
                        ->requiresConfirmation()
                        ->modalHeading('STOP! You are about to send a fire evacuation alert!')
                        ->modalDescription('You are about to send a fire evacuation alert! This will put the SELECTED screens in emergency mode and stop any currently playing content. This is reserved for emergencies only. Please confirm you want to send this alert.')
                        ->color('danger')
                        ->icon('heroicon-m-fire')
                        ->action(fn(
                            Collection $records
                        ) => SetEmergencyPlaylistJob::dispatchSync(Auth::user(), EmergencyTypeEnum::FIRE, $records))
                        ->schema(array(
                            Checkbox::make('sensecheck')->required()->label('I am about to send an EMERGENCY ALERT!')->hintColor('danger')->hint('DANGER')->helperText('This is a serious action and should only be used in emergencies.'),
                        )),
                    BulkAction::make('GeneralEmergencyAlert')
                        ->label('General Evacuation')
                        ->requiresConfirmation()
                        ->modalHeading('STOP! You are about to send an evacuation alert!')
                        ->modalDescription('You are about to send an emergency alert! This will put the SELECTED screens in emergency mode and stop any currently playing content. This is reserved for emergencies only. Please confirm you want to send this alert.')
                        ->schema(array(
                            Checkbox::make('sensecheck')->required()->label('I am about to send an EMERGENCY ALERT!')->hintColor('danger')->hint('DANGER')->helperText('This is a serious action and should only be used in emergencies.'),
                        ))
                        ->color('danger')
                        ->icon('heroicon-s-arrow-right-on-rectangle')
                        ->action(fn(
                            Collection $records
                        ) => SetEmergencyPlaylistJob::dispatchSync(Auth::user(), EmergencyTypeEnum::EVACUATION,
                            $records)),
                    BulkAction::make('CustomEmergencyAlert')
                        ->label('Custom Emergency Alert')
                        ->requiresConfirmation()
                        ->modalHeading('STOP! You are about to send an emergency alert!')
                        ->modalDescription('You are about to send an emergency alert! This will put the SELECTED screens in emergency mode and stop any currently playing content. This is reserved for emergencies only. Please confirm you want to send this alert.')
                        ->modalSubmitActionLabel('Confirm Send Alert')
                        ->action(fn(
                            Collection $records,
                            array $data
                        ) => SetEmergencyPlaylistJob::dispatchSync(Auth::user(),
                            EmergencyTypeEnum::CUSTOM,
                            $records,
                            $data['message'],
                            $data['title']
                        ))
                        ->schema(array(
                            TextInput::make('title')
                                ->label('Title')
                                ->required(),
                            Textarea::make('message')
                                ->label('Message')
                                ->required(),
                            Checkbox::make('sensecheck')->required()->label('I am about to send an EMERGENCY ALERT!')->hintColor('danger')->hint('DANGER')->helperText('This is a serious action and should only be used in emergencies.'),
                        ))
                        ->color('danger')
                        ->icon('heroicon-o-document-text'),
                    BulkAction::make('TestEmergencyAlert')
                        ->label('Test Emergency System')
                        ->requiresConfirmation()
                        ->modalHeading('STOP! You are about to send an emergency alert!')
                        ->modalDescription('You are about to send an emergency alert! This will put the SELECTED screens in emergency mode and stop any currently playing content. This is reserved for emergencies only. Please confirm you want to send this alert.')
                        ->modalSubmitActionLabel('Confirm Send Alert')
                        ->action(fn(
                            Collection $records
                        ) => SetEmergencyPlaylistJob::dispatchSync(Auth::user(), EmergencyTypeEnum::TEST, $records))
                        ->schema(array(
                            Checkbox::make('sensecheck')->required()->label('I am about to send an TEST ALERT. ONLY USE THIS FOR TESTING, SCREENS WILL STILL JUMP INTO EMERGENCY MODE!')->hintColor('danger')->hint('DANGER')->helperText('This is a serious action and should only be used in emergencies.'),
                        ))
                        ->color('warning')
                        ->icon('heroicon-o-document-text'),
                    BulkAction::make('LiftEmergencyAlert')
                        ->label('Emergency Over Alert')
                        ->requiresConfirmation()
                        ->modalHeading('You are about to send an emergency over alert.')
                        ->modalDescription('This will display a message on the screens that the emergency is over.')
                        ->modalSubmitActionLabel('Danger is over, lift alert')
                        ->action(fn(
                            Collection $records
                        ) => SetEmergencyPlaylistJob::dispatchSync(Auth::user(), EmergencyTypeEnum::LIFTED, $records))
                        ->schema(array(
                            Checkbox::make('sensecheck')->required()->label('There is no more danger, shows a lifted message on the screens.')->hintColor('danger')->hint('DANGER')->helperText('This is a serious action and should only be used in emergencies.'),
                        ))
                        ->color('warning')
                        ->icon('heroicon-o-document-text'),
                    BulkAction::make('ReturnRegularOperation')
                        ->label('Disable Emergency Mode')
                        ->requiresConfirmation()
                        ->modalHeading('You are about to disable the emergency mode.')
                        ->modalDescription('This will disable the emergency mode on the screens you selected and return them to normal operation.')
                        ->modalSubmitActionLabel('Confirm')
                        ->action(fn(
                            Collection $records
                        ) => SetEmergencyPlaylistJob::dispatchSync(Auth::user(), EmergencyTypeEnum::NONE, $records))
                        ->schema(array(
                            Checkbox::make('sensecheck')->required()->label('Return screens to normal operation.')->hintColor('success')->hint('DANGER')->helperText('This is a serious action and should only be used in emergencies.'),
                        ))
                        ->color('success')
                        ->icon('heroicon-o-document-text'),
                ))
                    ->icon('heroicon-s-arrow-right-on-rectangle')
                    ->tooltip('Public Health and Safety Announcements, these are reserved only for emergencies.')
                    ->color('danger')
                    ->label('Emergency Alerts'),
            ));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListScreens::route('/'),
            'create' => CreateScreen::route('/create'),
            'edit' => EditScreen::route('/{record}/edit'),
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
