<?php

namespace App\Filament\Pages;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use App\Models\Playlist;
use App\Services\ScreenTabResource;
use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Pages\SettingsPage;

class ManageSite extends SettingsPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = GeneralSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Event Name')
                    ->required()
                    ->columnSpanFull()
                    ->maxValue(255)
                    ->placeholder('Open Signage Event'),
                DateTimePicker::make('starts_at')
                    ->label('Event Starts At')
                    ->required()
                    ->hint('Can be used by pages to determine when to show certain data.')
                    ->default(now()),
                DateTimePicker::make('ends_at')
                    ->label('Event Ends At')
                    ->required()
                    ->hint('Can be used by pages to determine when to show certain data.')
                    ->default(now()),
                Select::make('playlist_id')
                    ->label('Default Playlist for new Screens')
                    ->options(
                        Playlist::all()->pluck('name', 'id')->toArray()
                    ),
                TextEntry::make('notice')
                    ->hiddenLabel()
                    ->state(fn () => 'These are defaults that can be overridden per case basis.')
                    ->columnSpanFull(),
                ScreenTabResource::getForm(),
            ]);
    }
}
