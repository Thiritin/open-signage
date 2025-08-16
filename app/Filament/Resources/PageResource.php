<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\PageResource\Pages\ListPages;
use App\Filament\Resources\PageResource\Pages\CreatePage;
use App\Filament\Resources\PageResource\Pages\EditPage;
use App\Enums\ResourceOwnership;
use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use App\Models\Project;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Development';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $slug = 'pages';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('project_id')
                    ->relationship('project', 'name', fn ($query) => $query->where('type', ResourceOwnership::USER))
                    ->default(Project::where('path', config('app.default_project'))->firstOrFail()->id)
                    ->hint('Autofilled by default, but you can change it if you want.')
                    ->createOptionForm(function () {
                        return [
                            Grid::make()->columns()->schema([
                                TextInput::make('name')
                                    ->placeholder('Wild Times 2023')
                                    ->required(),
                                TextInput::make('path')
                                    ->placeholder('wt23')
                                    ->unique('projects', 'path')
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
                            'FileInput' => 'File',
                            'ImageInput' => 'Image',
                            'DatePicker' => 'Date',
                            'RichEditor' => 'Rich Text (HTML)',
                        ])
                        ->required(),
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
                    ->formatStateUsing(fn ($record) => $record->project->name)
                    ->color(fn ($state) => match ($state->value) {
                        'emergency' => 'danger',
                        'system' => 'gray',
                        'user' => 'success',
                    }),
            ])->filters([
                SelectFilter::make('project')
                    ->relationship('project', 'name', fn ($query) => $query->where('type', '!=', ResourceOwnership::EMERGENCY))
                    ->default(Project::where('path', config('app.default_project'))->firstOrFail()->id),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
