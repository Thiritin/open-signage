<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\LayoutResource\Pages\ListLayouts;
use App\Filament\Resources\LayoutResource\Pages\CreateLayout;
use App\Filament\Resources\LayoutResource\Pages\EditLayout;
use App\Enums\ResourceOwnership;
use App\Filament\Resources\LayoutResource\Pages;
use App\Models\Layout;
use App\Models\Project;
use Exception;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LayoutResource extends Resource
{
    protected static ?string $model = Layout::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Development';

    protected static ?string $slug = 'layouts';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document';

    protected static ?string $recordTitleAttribute = 'name';

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

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn (?Layout $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn (?Layout $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

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
                    ->relationship('project', 'name',
                        fn ($query) => $query->where('type', '!=', ResourceOwnership::EMERGENCY))
                    ->default(Project::where('path', config('app.default_project'))->firstOrFail()->id),
            ])->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLayouts::route('/'),
            'create' => CreateLayout::route('/create'),
            'edit' => EditLayout::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
