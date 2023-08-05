<?php

namespace App\Filament\Pages;

use App\Enums\ResourceOwnership;
use App\Services\ScreenTabResource;
use App\Settings\GeneralSettings;
use Closure;
use DateTimeZone;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\SettingsPage;
use Filament\Tables\Columns\SelectColumn;

class ManageSite extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = GeneralSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Event Name')
                    ->required()
                    ->columnSpanFull()
                    ->maxValue(255)
                    ->placeholder('Open Signage Event'),
                Forms\Components\DateTimePicker::make('starts_at')
                    ->label('Event Starts At')
                    ->required()
                    ->hint('Can be used by pages to determine when to show certain data.')
                    ->default(now()),
                Forms\Components\DateTimePicker::make('ends_at')
                    ->label('Event Ends At')
                    ->required()
                    ->hint('Can be used by pages to determine when to show certain data.')
                    ->default(now()),
                Forms\Components\Select::make('playlist_id')
                    ->label('Default Playlist for new Screens')
                    ->options(
                        \App\Models\Playlist::all()->pluck('name', 'id')->toArray()
                    ),
                Forms\Components\Select::make('project_id')
                    ->label('Project')
                    ->options(
                        \App\Models\Project::where('type','=',ResourceOwnership::USER)->pluck('name', 'id')->toArray()
                    ),
                Forms\Components\Placeholder::make('notice')->columnSpanFull()->label('These are defaults that can be overriden per case basis.'),
                ScreenTabResource::getForm(),
            ]);
    }
}
