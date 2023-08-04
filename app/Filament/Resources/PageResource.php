<?php

namespace App\Filament\Resources;

use App\Enums\ResourceOwnership;
use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use App\Settings\GeneralSettings;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;
    protected static ?string $navigationGroup = "Development";

    protected static ?string $navigationIcon = "heroicon-o-document-text";
    protected static ?string $slug = 'pages';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('project_id')
                    ->relationship('project', 'name', fn($query) => $query->where('type', ResourceOwnership::USER))
                    ->default(app(GeneralSettings::class)->project_id)
                    ->hint('Autofilled by default, but you can change it if you want.')
                    ->createOptionForm(function () {
                        return [
                            Grid::make()->columns()->schema([
                                TextInput::make('name')
                                    ->placeholder('Wild Times 2023')
                                    ->required(),
                                TextInput::make('path')
                                    ->placeholder('wt23')
                                    ->unique('projects','path')
                                    ->required(),
                            ]),
                        ];
                    })->columnSpanFull()
                    ->required(),

                TextInput::make('name')
                    ->required(),

                TextInput::make('component')
                    ->required(),

                Repeater::make('schema')->columnSpanFull()->schema([
                    TextInput::make('name')
                        ->hint('Displayed in the field label')
                        ->required(),
                    TextInput::make('property')
                        ->hint('Vue.js Property Name')
                        ->required(),
                    Select::make('type')
                        ->options([
                            'TextInput' => 'Text',
                            'Textarea' => 'Textarea',
                            'Checkbox' => 'Checkbox',
                        ])
                        ->required()
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),

                TextColumn::make('component'),

                TextColumn::make('project.type')->badge()
                    ->formatStateUsing(fn($record) => $record->project->name)
                    ->color(fn ($state) => match ($state->value) {
                        'emergency' => 'danger',
                        'system' => 'gray',
                        'user' => 'success',
                    })
            ])->filters([
                SelectFilter::make('project')
                    ->relationship('project', 'name',fn($query) => $query->where('type','!=', ResourceOwnership::EMERGENCY))
                    ->default(app(GeneralSettings::class)->project_id)
            ])
            ->actions([
                EditAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
